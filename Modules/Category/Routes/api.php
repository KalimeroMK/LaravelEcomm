<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\CategoryController;

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

Route::apiResource('categories', CategoryController::class)
    ->names([
        'index' => 'api.category.index',
        'store' => 'api.category.store',
        'show' => 'api.category.show',
        'destroy' => 'api.category.destroy',
        'update' => 'api.category.update', // if you're using forms for APIs, which is unusual
        'create' => 'api.category.create', // if you're using forms for APIs
    ]);
