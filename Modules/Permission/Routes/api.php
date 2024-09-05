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

use Modules\Permission\Http\Controllers\Api\PermissionController;

Route::apiResource('permissions', PermissionController::class)->names('api.permission');
