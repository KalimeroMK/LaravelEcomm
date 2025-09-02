<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Newsletter\Http\Controllers\EmailTrackingController;
use Modules\Newsletter\Http\Controllers\NewsletterController;

Route::resource('newsletters', NewsletterController::class)->except('show');

// Email tracking routes
Route::get('email/track/open', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('email/track/click', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');
Route::get('email/unsubscribe', [EmailTrackingController::class, 'unsubscribe'])->name('email.unsubscribe');
