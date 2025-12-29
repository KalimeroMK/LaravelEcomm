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

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\EmailSettingsController;
use Modules\Settings\Http\Controllers\PaymentSettingsController;
use Modules\Settings\Http\Controllers\SeoSettingsController;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Http\Controllers\ShippingSettingsController;

Route::resource('settings', SettingsController::class)->only('index', 'update');

// Payment Settings
Route::get('settings/payment', [PaymentSettingsController::class, 'index'])->name('settings.payment.index');
Route::put('settings/payment/{setting}', [PaymentSettingsController::class, 'update'])->name('settings.payment.update');

// Shipping Settings
Route::get('settings/shipping', [ShippingSettingsController::class, 'index'])->name('settings.shipping.index');
Route::put('settings/shipping/{setting}', [ShippingSettingsController::class, 'update'])->name('settings.shipping.update');

// Email Settings
Route::get('settings/email', [EmailSettingsController::class, 'index'])->name('settings.email.index');
Route::put('settings/email/{setting}', [EmailSettingsController::class, 'update'])->name('settings.email.update');

// SEO Settings
Route::get('settings/seo', [SeoSettingsController::class, 'index'])->name('settings.seo.index');
Route::put('settings/seo/{setting}', [SeoSettingsController::class, 'update'])->name('settings.seo.update');

// Database Management (no migrations/seeds)
Route::prefix('settings/database')->name('settings.database.')->group(function () {
    Route::get('/', [Modules\Settings\Http\Controllers\DatabaseManagementController::class, 'index'])->name('index');
    Route::post('migrate', [Modules\Settings\Http\Controllers\DatabaseManagementController::class, 'migrate'])->name('migrate');
    Route::post('migrate-fresh', [Modules\Settings\Http\Controllers\DatabaseManagementController::class, 'migrateFresh'])->name('migrate-fresh');
    Route::post('seed', [Modules\Settings\Http\Controllers\DatabaseManagementController::class, 'seed'])->name('seed');
    Route::post('migrate-fresh-seed', [Modules\Settings\Http\Controllers\DatabaseManagementController::class, 'migrateFreshSeed'])->name('migrate-fresh-seed');
});
