<?php

use Modules\Shipping\Http\Controllers\Api\ShippingController;

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

Route::apiResource('shipping', ShippingController::class)->names(
    [
        'index' => 'api.shipping.index',
        'store' => 'api.shipping.store',
        'show' => 'api.shipping.show',
        'update' => 'api.shipping.update',
        'destroy' => 'api.shipping.destroy',
    ]
);
