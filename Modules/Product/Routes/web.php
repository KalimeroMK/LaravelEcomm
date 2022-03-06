<?php

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
    use Modules\Product\Http\Controllers\ProductController;
    use Modules\Product\Http\Controllers\ProductReviewController;

    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::resource('/products', ProductController::class);
    });
// Product Review
    Route::post('product/{slug}/review', [ProductReviewController::class, 'store'])->name('product.review.store');
    Route::resource('/review', ProductReviewController::class);
