<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\AnalyticsDemoDataSeeder;

class GenerateAnalyticsDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:demo-data {--fresh : Clear existing data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate demo data for Analytics Dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Analytics Dashboard demo data...');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing data...');
            $this->clearExistingData();
        }

        $seeder = new AnalyticsDemoDataSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('Demo data generated successfully!');
        $this->line('');
        $this->line('You can now view the Analytics Dashboard with demo data.');
        $this->line('Visit: /admin/analytics');
    }

    private function clearExistingData()
    {
        // Clear existing demo data (be careful with this in production)
        \Modules\Order\Models\OrderItem::truncate();
        \Modules\Order\Models\Order::truncate();

        // Keep some users but clear demo ones (those created in the last 2 years)
        \Modules\User\Models\User::where('created_at', '>', now()->subYears(2))->delete();

        // Clear demo products
        \Modules\Product\Models\Product::where('sku', 'like', 'DEMO-%')->delete();

        $this->info('Existing demo data cleared.');
    }
}
