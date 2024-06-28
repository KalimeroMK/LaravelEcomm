<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Api\OrderController;

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

Route::apiResource('orders', OrderController::class)
    ->names([
        'index' => 'api.order.index',
        'store' => 'api.order.store',
        'show' => 'api.order.show',
        'destroy' => 'api.order.destroy',
        'update' => 'api.order.update',
        'create' => 'api.order.create',
    ]);
