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

Route::apiResource('product', ProductController::class)->except('update');
Route::post('product/{id}', [ProductController::class, 'update'])->name('product.update');
