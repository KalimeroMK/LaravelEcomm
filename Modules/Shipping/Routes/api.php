<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
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

// API Routes
Route::apiResource('shipping', ShippingController::class)->names(
    [
        'index' => 'api.shipping.index',
        'store' => 'api.shipping.store',
        'show' => 'api.shipping.show',
        'update' => 'api.shipping.update',
        'destroy' => 'api.shipping.destroy',
    ]
);

// Shipping calculation API
Route::post('shipping/calculate', [Modules\Shipping\Http\Controllers\Api\ShippingCalculationController::class, 'calculate'])
    ->name('api.shipping.calculate');
