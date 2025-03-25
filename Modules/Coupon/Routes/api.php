<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Api\CouponController;

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

Route::apiResource('coupons', CouponController::class)
    ->names('api.coupons');
