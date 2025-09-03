<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\Api\StripeController;
use Modules\Billing\Http\Controllers\Api\WishlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Routes
    // Enhanced Wishlist Routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('api.wishlist.index');
        Route::post('/', [WishlistController::class, 'store'])->name('api.wishlist.store');
        Route::put('/{id}', [WishlistController::class, 'update'])->name('api.wishlist.update');
        Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');

        // Advanced wishlist features
        Route::get('/count', [WishlistController::class, 'count'])->name('api.wishlist.count');
        Route::get('/check/{id}', [WishlistController::class, 'check'])->name('api.wishlist.check');
        Route::post('/{id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('api.wishlist.move-to-cart');
        Route::get('/recommendations', [WishlistController::class, 'recommendations'])->name('api.wishlist.recommendations');
        Route::post('/bulk-operations', [WishlistController::class, 'bulkOperations'])->name('api.wishlist.bulk-operations');
        Route::post('/share', [WishlistController::class, 'share'])->name('api.wishlist.share');
        Route::get('/public/{username}', [WishlistController::class, 'publicWishlist'])->name('api.wishlist.public');
        Route::get('/price-alerts', [WishlistController::class, 'priceAlerts'])->name('api.wishlist.price-alerts');
    });

    Route::post('stripe', [StripeController::class, 'stripe']);