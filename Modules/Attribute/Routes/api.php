<?php

use Modules\Attribute\Http\Controllers\Api\AttributeController;

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

Route::apiResource('attribute', AttributeController::class)
    ->names([
        'index' => 'api.attribute.index',
        'store' => 'api.attribute.store',
        'show' => 'api.attribute.show',
        'destroy' => 'api.attribute.destroy',
        'update' => 'api.attribute.update',
        'create' => 'api.attribute.create',
    ]);