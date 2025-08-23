<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunEcommerceTests extends Command
{
    protected $signature = 'ecommerce:test 
                            {--module=all : Specific module to test (cart, order, payment, product, workflow, business)}
                            {--coverage : Generate coverage report}
                            {--parallel : Run tests in parallel}
                            {--filter= : Filter tests by name}';

    protected $description = 'Run comprehensive e-commerce API tests with detailed reporting';

    public function handle(): int
    {
        $this->info('🧪 Starting E-commerce API Test Suite...');
        $this->newLine();

        $module = $this->option('module');
        $coverage = $this->option('coverage');
        $parallel = $this->option('parallel');
        $filter = $this->option('filter');

        $this->displayTestPlan($module);
        $this->newLine();

        try {
            $results = $this->runTests($module, $coverage, $parallel, $filter);
            $this->displayResults($results);

            return $results['success'] ? Command::SUCCESS : Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Test execution failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function displayTestPlan(string $module): void
    {
        $this->info('📋 Test Plan:');

        $modules = [
            'cart' => 'Cart operations (add, update, remove, view)',
            'order' => 'Order creation and management',
            'payment' => 'Payment processing and validation',
            'product' => 'Product listing, search, and filtering',
            'workflow' => 'End-to-end e-commerce workflow',
            'business' => 'Business logic validation'
        ];

        if ($module === 'all') {
            foreach ($modules as $name => $description) {
                $this->line("  • {$name}: {$description}");
            }
        } else {
            if (isset($modules[$module])) {
                $this->line("  • {$module}: {$modules[$module]}");
            } else {
                $this->warn("  ⚠️ Unknown module: {$module}");
            }
        }
    }

    private function runTests(string $module, bool $coverage, bool $parallel, ?string $filter): array
    {
        $startTime = microtime(true);
        $results = [
            'success' => true,
            'total' => 0,
            'passed' => 0,
            'failed' => 0,
            'skipped' => 0,
            'time' => 0,
            'coverage' => null
        ];

        $testCommands = $this->getTestCommands($module);

        foreach ($testCommands as $testCommand) {
            $this->info("🔍 Running: {$testCommand}");

            $command = $this->buildTestCommand($testCommand, $coverage, $parallel, $filter);
            $exitCode = Artisan::call($command);

            if ($exitCode !== 0) {
                $results['success'] = false;
                $results['failed']++;
            } else {
                $results['passed']++;
            }

            $results['total']++;

            $this->newLine();
        }

        $results['time'] = round(microtime(true) - $startTime, 2);

        if ($coverage) {
            $results['coverage'] = $this->generateCoverageReport();
        }

        return $results;
    }

    private function getTestCommands(string $module): array
    {
        $commands = [
            'cart' => ['Modules/Cart/Tests/Feature/CartApiTest.php'],
            'order' => ['Modules/Order/Tests/Feature/OrderApiTest.php'],
            'payment' => ['Modules/Billing/Tests/Feature/PaymentApiTest.php'],
            'product' => ['Modules/Product/Tests/Feature/ProductApiTest.php'],
            'workflow' => ['Modules/Core/Tests/Feature/EcommerceWorkflowTest.php'],
            'business' => ['Modules/Core/Tests/Feature/BusinessLogicValidationTest.php']
        ];

        if ($module === 'all') {
            return array_merge(...array_values($commands));
        }

        return $commands[$module] ?? [];
    }

    private function buildTestCommand(string $testFile, bool $coverage, bool $parallel, ?string $filter): string
    {
        $command = "test {$testFile}";

        if ($coverage) {
            $command .= ' --coverage-text';
        }

        if ($parallel) {
            $command .= ' --parallel';
        }

        if ($filter) {
            $command .= " --filter=\"{$filter}\"";
        }

        return $command;
    }

    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Test Results Summary:');
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $results['total']],
                ['Passed', "✅ {$results['passed']}"],
                ['Failed', "❌ {$results['failed']}"],
                ['Skipped', "⏭️ {$results['skipped']}"],
                ['Execution Time', "⏱️ {$results['time']}s"],
                ['Overall Status', $results['success'] ? '✅ PASSED' : '❌ FAILED']
            ]
        );

        if ($results['coverage']) {
            $this->newLine();
            $this->info('📈 Coverage Report:');
            $this->line($results['coverage']);
        }

        if (!$results['success']) {
            $this->newLine();
            $this->warn('⚠️ Some tests failed. Check the output above for details.');
            $this->info('💡 Run individual module tests to isolate issues:');
            $this->line('   php artisan ecommerce:test --module=cart');
            $this->line('   php artisan ecommerce:test --module=order');
        }
    }

    private function generateCoverageReport(): string
    {
        try {
            $output = Artisan::call('test --coverage-text');
            return Artisan::output();
        } catch (\Exception $e) {
            return 'Coverage report generation failed: ' . $e->getMessage();
        }
    }
}
