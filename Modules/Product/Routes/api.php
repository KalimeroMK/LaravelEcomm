<?php

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

use Modules\Product\Http\Controllers\Api\ProductController;

Route::apiResource('products', ProductController::class)
    ->except('update')
    ->names([
        'index' => 'api.product.index',
        'store' => 'api.product.store',
        'show' => 'api.product.show',
        'create' => 'api.product.create',
        'destroy' => 'api.product.destroy',
    ]);
Route::post('products/{id}', [ProductController::class, 'update'])->name('api.product.update');
