<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\CategoryController;

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
Route::apiResource('categories', CategoryController::class)
    ->names('api.categories');

// Category Tree and Order Routes
Route::prefix('categories')->group(function (): void {
    Route::get('tree', [CategoryController::class, 'tree'])->name('api.categories.tree');
    Route::post('order/update', [CategoryController::class, 'updateOrder'])->name('api.categories.order');
});
