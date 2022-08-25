<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\CouponController;
use Modules\Front\Http\Controllers\Api\FrontController;

Route::prefix('admin')->middleware(['auth', 'doNotCacheResponse'])->group(function () {
    Route::resource('coupons', CouponController::class)->except('show');
});
Route::post('/coupon-store', [FrontController::class, 'couponStore'])->name('coupon-store');
