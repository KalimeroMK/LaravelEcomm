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

use Modules\Message\Http\Controllers\Api\MessageController;

Route::apiResource('message', MessageController::class)->only('index', 'destroy', 'show');

