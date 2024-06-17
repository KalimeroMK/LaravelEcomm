<?php

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

Route::apiResource('messages', MessageController::class)->names([
    'index' => 'api.message.index',
    'create' => 'api.message.create',
    'store' => 'api.message.store',
    'show' => 'api.message.show',
    'update' => 'api.message.update',
    'destroy' => 'api.message.destroy',
]);;

