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
use Modules\Message\Http\Controllers\MessageController;

Route::resource('messages', MessageController::class)->only('index', 'show', 'destroy');
Route::put('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
Route::get('messages/{message}', [MessageController::class, 'show'])->name('message.show');
Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('message.destroy');
Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
Route::post('messages/mark-read', [MessageController::class, 'markMultipleAsRead'])->name('messages.markMultipleAsRead');
