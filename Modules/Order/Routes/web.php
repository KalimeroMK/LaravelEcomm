<?php

declare(strict_types=1);

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

// Admin routes (these will be prefixed with /admin in RouteServiceProvider)
Route::resource('/orders', OrderController::class);

// Return/refund requests (RMA)
Route::get('/order-returns', [Modules\Order\Http\Controllers\OrderReturnController::class, 'index'])->name('order-returns.index');
Route::post('/order-returns/{orderReturn}/approve', [Modules\Order\Http\Controllers\OrderReturnController::class, 'approve'])->name('order-returns.approve');
Route::post('/order-returns/{orderReturn}/reject', [Modules\Order\Http\Controllers\OrderReturnController::class, 'reject'])->name('order-returns.reject');
Route::post('/order-returns/{orderReturn}/refund', [Modules\Order\Http\Controllers\OrderReturnController::class, 'refund'])->name('order-returns.refund');
Route::get('orders/pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
Route::get('/income', [OrderController::class, 'incomeChart'])->name('product.order.income');
