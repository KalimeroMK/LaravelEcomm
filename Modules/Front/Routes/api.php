<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\Api\ProductFilterController;

// use Modules\Front\Http\Controllers\Api\CartController;
// use Modules\Front\Http\Controllers\Api\ReviewController;
// use Modules\Front\Http\Controllers\Api\WishlistController;

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

// Cart API - controllers need to be created
// Route::prefix('cart')->group(function () {
//     Route::get('/', [CartController::class, 'index'])->name('front.api.cart.index');
// });

// Reviews API - controllers need to be created
// Route::prefix('reviews')->group(function () {
//     Route::get('/product/{productId}', [ReviewController::class, 'index'])->name('front.api.reviews.index');
// });

// Wishlist API - controllers need to be created
// Route::prefix('wishlist')->middleware('auth')->group(function () {
//     Route::get('/', [WishlistController::class, 'index'])->name('front.api.wishlist.index');
// });
