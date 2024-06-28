<?php

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
    ->names([
        'index' => 'api.coupon.index',
        'store' => 'api.coupon.store',
        'show' => 'api.coupon.show',
        'destroy' => 'api.coupon.destroy',
        'update' => 'api.coupon.update', // if you're using forms for APIs, which is unusual
        'create' => 'api.coupon.create', // if you're using forms for APIs
    ]);
