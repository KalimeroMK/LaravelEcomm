<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ProductStats\Http\Controllers\Api\ProductStatsController;
use Modules\ProductStats\Http\Controllers\Api\ProductTrackingController;

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

// Product Stats Routes
Route::prefix('product-stats')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [ProductStatsController::class, 'index'])->name('api.product-stats.index');
    Route::get('{id}/detail', [ProductStatsController::class, 'detail'])->name('api.product-stats.detail');
});

// Product Tracking Routes (can work without authentication)
Route::prefix('product-tracking')->group(function (): void {
    Route::post('impressions', [ProductTrackingController::class, 'storeImpressions'])->name('api.product-tracking.impressions');
    Route::post('click', [ProductTrackingController::class, 'storeClick'])->name('api.product-tracking.click');
});
