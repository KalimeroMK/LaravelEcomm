<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ProductStats\Http\Controllers\ProductStatsController;
use Modules\ProductStats\Http\Controllers\ProductStatsController as AdminProductStatsController;

// ProductStatsController does no authorization of its own, so it is gated here.
Route::middleware('role_or_permission:admin|super-admin|product-stats-list')->group(function (): void {
    Route::get('product-stats', [AdminProductStatsController::class, 'index'])->name('product-stats.index');
    Route::get('product-stats/{id}/detail', [ProductStatsController::class, 'detail'])->name('product-stats.detail');
    Route::resource('productstats', ProductStatsController::class)->names('productstats');
});
