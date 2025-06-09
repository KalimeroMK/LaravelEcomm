<?php

declare(strict_types=1);

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

use App\Controllers\ProductController;
use App\Controllers\ProductImportExportController;
use App\Controllers\ProductReviewController;

Route::prefix('admin')->middleware(['auth'])->group(function (): void {
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('reviews', ProductReviewController::class)->except('show', 'create');
    Route::delete('/products/{modelId}/media/{mediaId}',
        [ProductController::class, 'deleteMedia'])->name('product.delete-media');

    /* Excel import export */
    Route::get('products-export', [ProductImportExportController::class, 'export'])->name('product.export');
    Route::post('products-import', [ProductImportExportController::class, 'import'])->name('product.import');
    Route::get('products-import', [ProductImportExportController::class, 'index'])->name('export-import-product.index');
});
Route::post('reviews/{slug}', [ProductReviewController::class, 'store'])->name('product.review.store');
Route::post('generate-description',
    [ProductController::class, 'generateDescription'])->name('products.generate-description');
