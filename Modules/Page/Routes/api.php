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

Route::apiResource('pages', PageController::class)->except('update')->names([
    'index' => 'api.page.index',
    'store' => 'api.page.store',
    'show' => 'api.page.show',
    'destroy' => 'api.page.destroy',
    'create' => 'api.page.create',
]);
