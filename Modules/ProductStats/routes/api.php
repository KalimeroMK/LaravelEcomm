<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductStats\Http\Controllers\Api\ProductTrackingController;
use Modules\ProductStats\Http\Controllers\ProductStatsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('productstats', ProductStatsController::class)->names('productstats');
});

// Product tracking endpoints (public, for frontend JS tracking)
Route::prefix('tracking')->group(function () {
    Route::post('product-impressions',
        [ProductTrackingController::class, 'storeImpressions']);
    Route::post('product-click',
        [ProductTrackingController::class, 'storeClick']);
});
