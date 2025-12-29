<?php

declare(strict_types=1);

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

use Illuminate\Support\Facades\Route;
use Modules\Message\Http\Controllers\Api\MessageController;

// API Routes
Route::apiResource('messages', MessageController::class)->names('api.messages');
Route::put('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('api.messages.markAsRead');
Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('api.messages.reply');
Route::post('messages/mark-read', [MessageController::class, 'markMultipleAsRead'])->name('api.messages.markMultipleAsRead');
