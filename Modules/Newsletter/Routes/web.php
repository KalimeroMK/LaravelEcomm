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

use Modules\Newsletter\Http\Controllers\NewsletterAnalyticsController;
use Modules\Newsletter\Http\Controllers\NewsletterController;

Route::resource('newsletters', NewsletterController::class)->except('show');

// Newsletter Analytics
Route::get('newsletters/analytics', [NewsletterAnalyticsController::class, 'index'])->name('newsletters.analytics');

// Email Template and Campaign Routes are handled in RouteServiceProvider to avoid conflicts
