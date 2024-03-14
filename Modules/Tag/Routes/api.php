<?php

use Modules\Tag\Http\Controllers\Api\TagController;

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

Route::apiResource('tag', TagController::class)
    ->names([
        'index' => 'api.tag.index',
        'store' => 'api.tag.store',
        'show' => 'api.tag.show',
        'destroy' => 'api.tag.destroy',
        'update' => 'api.tag.update',
        'create' => 'api.tag.create',
    ]);

