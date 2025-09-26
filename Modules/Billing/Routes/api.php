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

// Enhanced Wishlist Routes (without authentication - checked in controller)
Route::get('wishlist/', [WishlistController::class, 'index'])->name('api.wishlist.index');
Route::post('wishlist/', [WishlistController::class, 'store'])->name('api.wishlist.store');
Route::put('wishlist/{id}', [WishlistController::class, 'update'])->name('api.wishlist.update');
Route::delete('wishlist/{id}', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');

// Advanced wishlist features
Route::get('wishlist/count', [WishlistController::class, 'count'])->name('api.wishlist.count');
Route::get('wishlist/check/{id}', [WishlistController::class, 'check'])->name('api.wishlist.check');
Route::post('wishlist/{id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('api.wishlist.move-to-cart');
Route::get('wishlist/recommendations', [WishlistController::class, 'recommendations'])->name('api.wishlist.recommendations');
Route::post('wishlist/bulk-operations', [WishlistController::class, 'bulkOperations'])->name('api.wishlist.bulk-operations');
Route::post('wishlist/share', [WishlistController::class, 'share'])->name('api.wishlist.share');
Route::get('wishlist/public/{username}', [WishlistController::class, 'publicWishlist'])->name('api.wishlist.public');
Route::get('wishlist/price-alerts', [WishlistController::class, 'priceAlerts'])->name('api.wishlist.price-alerts');
