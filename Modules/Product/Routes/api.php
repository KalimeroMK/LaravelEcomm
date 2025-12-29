<?php

declare(strict_types=1);

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

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\AdvancedSearchController;
use Modules\Product\Http\Controllers\Api\ProductComparisonController;
use Modules\Product\Http\Controllers\Api\ProductController;
use Modules\Product\Http\Controllers\Api\ProductImportExportController;
use Modules\Product\Http\Controllers\Api\ProductReviewController;

// API Routes - using plural form 'products' for RESTful convention
Route::apiResource('products', ProductController::class)->names([
    'index' => 'api.products.index',
    'store' => 'api.products.store',
    'show' => 'api.products.show',
    'update' => 'api.products.update',
    'destroy' => 'api.products.destroy',
]);

// Product Media Routes
Route::prefix('products')->group(function (): void {
    Route::delete('{modelId}/media/{mediaId}', [ProductController::class, 'deleteMedia'])->name('api.products.delete-media');
    Route::post('generate-description', [ProductController::class, 'generateDescription'])->name('api.products.generate-description');
});

// Product Review Routes
Route::apiResource('product-reviews', ProductReviewController::class)->names([
    'index' => 'api.product-reviews.index',
    'store' => 'api.product-reviews.store',
    'show' => 'api.product-reviews.show',
    'update' => 'api.product-reviews.update',
    'destroy' => 'api.product-reviews.destroy',
]);

// Product Import/Export Routes
Route::prefix('products')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('export', [ProductImportExportController::class, 'export'])->name('api.products.export');
    Route::post('import', [ProductImportExportController::class, 'import'])->name('api.products.import');
});

// Product Comparison Routes (public - uses cache with user ID or IP)
Route::prefix('products/compare')->group(function (): void {
    Route::post('add/{productId}', [ProductComparisonController::class, 'addToCompare'])->name('api.products.compare.add');
    Route::post('remove/{productId}', [ProductComparisonController::class, 'removeFromCompare'])->name('api.products.compare.remove');
    Route::get('/', [ProductComparisonController::class, 'showComparison'])->name('api.products.compare.show');
    Route::delete('/', [ProductComparisonController::class, 'clearComparison'])->name('api.products.compare.clear');
});

// Advanced Search and Recommendations Routes
Route::prefix('search')->group(function (): void {
    Route::post('/', [AdvancedSearchController::class, 'search'])->name('search.advanced');
    Route::get('/suggestions', [AdvancedSearchController::class, 'suggestions'])->name('search.suggestions');
    Route::get('/filters', [AdvancedSearchController::class, 'filters'])->name('search.filters');
});

Route::prefix('recommendations')->group(function (): void {
    Route::get('/', [AdvancedSearchController::class, 'recommendations'])->name('recommendations.index');
    Route::get('/related/{productId}', [AdvancedSearchController::class, 'relatedProducts'])->name('recommendations.related');
});
