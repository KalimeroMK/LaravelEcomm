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
use Modules\Permission\Http\Controllers\Api\PermissionController;

// API Routes
Route::apiResource('permissions', PermissionController::class)->names('api.permission');
