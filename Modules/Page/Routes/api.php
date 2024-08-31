<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\Api\PageController;

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

Route::apiResource('pages', PageController::class)->except('update')->names('api.pages');
