<?php

use Modules\Cart\Http\Controllers\Api\CartController;

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

Route::apiResource('carts', CartController::class)
    ->names([
        'index' => 'api.cart.index',
        'store' => 'api.cart.store',
        'show' => 'api.cart.show',
        'destroy' => 'api.cart.destroy',
        'update' => 'api.cart.update',
        'create' => 'api.cart.create',
    ]);
