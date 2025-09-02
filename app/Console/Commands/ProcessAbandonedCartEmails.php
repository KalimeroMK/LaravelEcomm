<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Cart\Services\AbandonedCartService;

class ProcessAbandonedCartEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:process-abandoned-emails 
                            {--stats : Show abandoned cart statistics}
                            {--cleanup : Clean up old abandoned carts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process abandoned cart emails and send them to customers';

    public function __construct(
        private readonly AbandonedCartService $abandonedCartService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🛒 Processing abandoned cart emails...');

        if ($this->option('stats')) {
            $this->showStats();
            return Command::SUCCESS;
        }

        if ($this->option('cleanup')) {
            $this->cleanupOldCarts();
            return Command::SUCCESS;
        }

        try {
            $this->abandonedCartService->processAbandonedCartEmails();
            $this->info('✅ Abandoned cart emails processed successfully!');

            // Show stats after processing
            $this->showStats();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error processing abandoned cart emails: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function showStats(): void
    {
        $stats = $this->abandonedCartService->getAbandonedCartStats();

        $this->newLine();
        $this->info('📊 Abandoned Cart Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Abandoned Carts', $stats['total_abandoned_carts']],
                ['Converted Carts', $stats['converted_carts']],
                ['Conversion Rate', $stats['conversion_rate'] . '%'],
                ['Pending First Email', $stats['pending_first_email']],
                ['Pending Second Email', $stats['pending_second_email']],
                ['Pending Third Email', $stats['pending_third_email']],
            ]
        );
    }

    private function cleanupOldCarts(): void
    {
        $this->info('🧹 Cleaning up old abandoned carts...');
        $this->abandonedCartService->cleanupOldAbandonedCarts();
        $this->info('✅ Old abandoned carts cleaned up successfully!');
    }
}
