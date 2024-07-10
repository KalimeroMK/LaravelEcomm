<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Newsletter\Console\PostNewsletterCommand;
use Modules\Newsletter\Console\ProductNewsletterCommand;
use Modules\Product\Console\StockNotifyCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        ProductNewsletterCommand::class,
        PostNewsletterCommand::class,
        StockNotifyCommand::class,

    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('sitemap:generate')->daily();
        $schedule->command('newsletter:product')->weekly();
        $schedule->command('newsletter:post')->weekly();
        $schedule->command('stock:notify')->daily()->onQueue('default');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
