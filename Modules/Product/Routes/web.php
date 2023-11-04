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

Route::prefix('admin')->middleware(['auth', 'doNotCacheResponse'])->group(function () {
    Route::resource('/products', ProductController::class)->except('show');
    // Product Review
    /*Excel import export*/
    Route::get('products/export', [ProductController::class, 'export'])->name('product.export');
    Route::post('products/import', [ProductController::class, 'import'])->name('product.import');

    Route::post('reviews/{slug}', [ProductReviewController::class, 'store'])->name('product.review.store');
    Route::resource('reviews', ProductReviewController::class)->except('show', 'create');
});

