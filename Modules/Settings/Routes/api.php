<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\Api\EmailSettingsController;
use Modules\Settings\Http\Controllers\Api\PaymentSettingsController;
use Modules\Settings\Http\Controllers\Api\SeoSettingsController;
use Modules\Settings\Http\Controllers\Api\SettingsController;
use Modules\Settings\Http\Controllers\Api\ShippingSettingsController;

/*
||--------------------------------------------------------------------------
|| API Routes
||--------------------------------------------------------------------------
||
|| Here is where you can register API routes for your application. These
|| routes are loaded by the RouteServiceProvider within a group which
|| is assigned the "api" middleware group. Enjoy building your API!
||
*/

// Email Settings Routes (must be before settings/{id} to avoid route conflicts)
Route::prefix('settings/email')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [EmailSettingsController::class, 'index'])->name('api.settings.email.index');
    Route::put('/', [EmailSettingsController::class, 'update'])->name('api.settings.email.update');
});

// Payment Settings Routes
Route::prefix('settings/payment')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [PaymentSettingsController::class, 'index'])->name('api.settings.payment.index');
    Route::put('/', [PaymentSettingsController::class, 'update'])->name('api.settings.payment.update');
});

// Shipping Settings Routes
Route::prefix('settings/shipping')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [ShippingSettingsController::class, 'index'])->name('api.settings.shipping.index');
    Route::put('/', [ShippingSettingsController::class, 'update'])->name('api.settings.shipping.update');
});

// SEO Settings Routes
Route::prefix('settings/seo')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [SeoSettingsController::class, 'index'])->name('api.settings.seo.index');
    Route::put('/', [SeoSettingsController::class, 'update'])->name('api.settings.seo.update');
});

// General Settings Routes (must be after specific settings routes)
Route::prefix('settings')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [SettingsController::class, 'index'])->name('api.settings.index');
    Route::put('{id}', [SettingsController::class, 'update'])->name('api.settings.update');
});
