<?php

declare(strict_types=1);

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

// API Routes
Route::apiResource('orders', OrderController::class)->names('api.orders');
