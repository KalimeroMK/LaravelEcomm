<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\Api\FrontController;
use Modules\Front\Http\Controllers\Api\ProductFilterController;

/*
|--------------------------------------------------------------------------
| API Routes - Front Module
|--------------------------------------------------------------------------
*/

// Product Filtering & Navigation
Route::prefix('products')->group(function () {
    // AJAX Product Filter
    Route::get('/filter', [ProductFilterController::class, 'filter'])
        ->name('front.api.products.filter');

    // Get Available Filters
    Route::get('/filters', [ProductFilterController::class, 'getFilters'])
        ->name('front.api.products.filters');

    // Get Price Range
    Route::get('/price-range', [ProductFilterController::class, 'getPriceRange'])
        ->name('front.api.products.price-range');
});

// Recently Viewed Products API
Route::get('recently-viewed', [FrontController::class, 'recentlyViewed'])
    ->name('front.api.recently-viewed');
