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
use Modules\Product\Http\Controllers\Api\ProductController;

// API Routes
Route::apiResource('product', ProductController::class);
Route::apiResource('products', ProductController::class);

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
