<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Bundle\Http\Controllers\Api\BundleController;

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

Route::apiResource('bundles', BundleController::class)->names('api.bundles');
