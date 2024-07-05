<?php

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Product\Mail\LowStockNotification;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class StockNotifyCommand extends Command
{
    protected $signature = 'stock:notify';

    protected $description = 'Check product stock and notify admin if stock is less than 10';

    public function handle(): void
    {
        $lowStockProducts = Product::where('stock', '<', 10)->get();
        $superAdmins = User::role('super-admin')->get();

        foreach ($superAdmins as $admin) {
            Mail::to($admin->email)->send(new LowStockNotification($lowStockProducts));
        }

        $this->info('Admin notified of low stock products.');
    }
}
