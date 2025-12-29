<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Api\OrderController;
use Modules\Order\Http\Controllers\Api\UserOrderController;

/*
||--------------------------------------------------------------------------
|| API Routes
||--------------------------------------------------------------------------
||
|| Here is where you can register API routes for your application. These
|| routes are loaded by the RouteServiceProvider within a group which
|| is assigned the "api" middleware group. Enjoy building your API!
||
*/

// API Routes
Route::apiResource('orders', OrderController::class)->names('api.orders');

// Order PDF and Analytics Routes
Route::prefix('orders')->group(function (): void {
    Route::get('{id}/pdf', [OrderController::class, 'pdf'])->name('api.orders.pdf');
    Route::get('income/chart', [OrderController::class, 'incomeChart'])->name('api.orders.income-chart');
});

// User Order Routes
Route::prefix('user/orders')->group(function (): void {
    Route::get('history', [UserOrderController::class, 'history'])->name('api.user.orders.history');
    Route::get('{id}/detail', [UserOrderController::class, 'detail'])->name('api.user.orders.detail');
    Route::get('{id}/track', [UserOrderController::class, 'track'])->name('api.user.orders.track');
});
