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
    use Modules\Order\Http\Controllers\OrderController;
    use Modules\Order\Http\Controllers\OrderFrontController;

    Route::prefix('admin')->middleware("auth")->group(function () {
        Route::resource('/orders', OrderController::class);
    });
    // Order Track
    Route::get('/product/track', [OrderFrontController::class, 'orderTrack'])->name('order.track');
    Route::post('product/track/order', [OrderFrontController::class, 'productTrackOrder'])->name('product.track.order');
