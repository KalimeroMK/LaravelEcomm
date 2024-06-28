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

use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductImportExportController;
use Modules\Product\Http\Controllers\ProductReviewController;

Route::prefix('admin')->middleware(['auth',])->group(function () {
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('reviews', ProductReviewController::class)->except('show', 'create');
    Route::delete('/products/{modelId}/media/{mediaId}',
        [ProductController::class, 'deleteMedia'])->name('product.delete-media');

    /*Excel import export*/
    Route::get('products-export', [ProductImportExportController::class, 'export'])->name('product.export');
    Route::post('products-import', [ProductImportExportController::class, 'import'])->name('product.import');
    Route::get('products-import', [ProductImportExportController::class, 'index'])->name('export-import-product.index');
});
Route::post('reviews/{slug}', [ProductReviewController::class, 'store'])->name('product.review.store');
