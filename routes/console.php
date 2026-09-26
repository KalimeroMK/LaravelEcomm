<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
*/

// Abandoned-cart recovery emails: dispatch due first/second/third reminders.
Schedule::command('cart:process-abandoned-emails')->hourly();

// Drop abandoned-cart records older than 30 days.
Schedule::call(fn () => app(\Modules\Cart\Services\AbandonedCartService::class)->cleanupOldAbandonedCarts())
    ->dailyAt('03:15')
    ->name('abandoned-carts-cleanup');
