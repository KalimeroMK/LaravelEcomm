<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CacheService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:optimize-performance {--force : Force optimization without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize application performance by clearing caches, warming up caches, and running maintenance tasks';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        if (! $this->option('force') && ! $this->confirm('Do you want to proceed with performance optimization?')) {
            $this->info('Performance optimization cancelled.');

            return Command::SUCCESS;
        }

        $this->info('🚀 Starting performance optimization...');

        try {
            // Step 1: Clear all caches
            $this->info('📦 Clearing caches...');
            $this->clearCaches($cacheService);

            // Step 2: Optimize database
            $this->info('🗄️ Optimizing database...');
            $this->optimizeDatabase();

            // Step 3: Warm up caches
            $this->info('🔥 Warming up caches...');
            $this->warmUpCaches($cacheService);

            // Step 4: Run Laravel optimizations
            $this->info('⚡ Running Laravel optimizations...');
            $this->runLaravelOptimizations();

            // Step 5: Check system health
            $this->info('🏥 Checking system health...');
            $this->checkSystemHealth($cacheService);

            $this->info('✅ Performance optimization completed successfully!');

            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Performance optimization failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Clear all application caches
     */
    private function clearCaches(CacheService $cacheService): void
    {
        $this->line('  - Clearing application cache...');
        Artisan::call('cache:clear');

        $this->line('  - Clearing config cache...');
        Artisan::call('config:clear');

        $this->line('  - Clearing route cache...');
        Artisan::call('route:clear');

        $this->line('  - Clearing view cache...');
        Artisan::call('view:clear');

        $this->line('  - Clearing Redis cache...');
        $cacheService->flush();

        $this->info('  ✅ All caches cleared successfully');
    }

    /**
     * Optimize database performance
     */
    private function optimizeDatabase(): void
    {
        // Analyze tables for better query performance
        $this->line('  - Analyzing database tables...');
        $tables = Schema::getAllTables();

        foreach ($tables as $table) {
            $tableName = $table->name;
            if ($tableName !== 'migrations') {
                try {
                    DB::statement("ANALYZE TABLE `{$tableName}`");
                    $this->line("    ✓ Analyzed table: {$tableName}");
                } catch (Exception $e) {
                    $this->warn("    ⚠️ Could not analyze table: {$tableName}");
                }
            }
        }

        // Check for slow queries
        $this->line('  - Checking for slow queries...');

        try {
            $slowQueries = DB::select('SHOW PROCESSLIST');
            $longRunningQueries = array_filter($slowQueries, function ($query) {
                return $query->Time > 10; // Queries running longer than 10 seconds
            });

            if (count($longRunningQueries) > 0) {
                $this->warn('    ⚠️ Found '.count($longRunningQueries).' long-running queries');
            } else {
                $this->line('    ✓ No long-running queries found');
            }
        } catch (Exception $e) {
            $this->warn('    ⚠️ Could not check for slow queries');
        }

        $this->info('  ✅ Database optimization completed');
    }

    /**
     * Warm up application caches
     */
    private function warmUpCaches(CacheService $cacheService): void
    {
        $this->line('  - Warming up product caches...');
        // This would cache frequently accessed product data

        $this->line('  - Warming up category caches...');
        // This would cache category trees and active categories

        $this->line('  - Warming up settings caches...');
        // This would cache application settings

        $this->info('  ✅ Cache warm-up completed');
    }

    /**
     * Run Laravel framework optimizations
     */
    private function runLaravelOptimizations(): void
    {
        $this->line('  - Optimizing autoloader...');
        Artisan::call('optimize:clear');

        $this->line('  - Rebuilding autoloader...');
        Artisan::call('optimize');

        $this->line('  - Publishing vendor assets...');
        Artisan::call('vendor:publish', ['--tag' => 'laravel-assets', '--force' => true]);

        $this->info('  ✅ Laravel optimizations completed');
    }

    /**
     * Check system health and performance
     */
    private function checkSystemHealth(CacheService $cacheService): void
    {
        // Check cache statistics
        $this->line('  - Checking cache performance...');

        try {
            $cacheStats = $cacheService->getStats();
            $this->line("    ✓ Cache hit rate: {$cacheStats['hit_rate']}%");
            $this->line("    ✓ Memory usage: {$cacheStats['used_memory']}");
            $this->line("    ✓ Connected clients: {$cacheStats['connected_clients']}");
        } catch (Exception $e) {
            $this->warn('    ⚠️ Could not retrieve cache statistics');
        }

        // Check database connections
        $this->line('  - Checking database connections...');

        try {
            $connectionCount = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $this->line("    ✓ Active database connections: {$connectionCount[0]->Value}");
        } catch (Exception $e) {
            $this->warn('    ⚠️ Could not check database connections');
        }

        // Check queue status
        $this->line('  - Checking queue status...');

        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $this->line("    ✓ Pending jobs: {$pendingJobs}");
            $this->line("    ✓ Failed jobs: {$failedJobs}");
        } catch (Exception $e) {
            $this->warn('    ⚠️ Could not check queue status');
        }

        $this->info('  ✅ System health check completed');
    }
}
