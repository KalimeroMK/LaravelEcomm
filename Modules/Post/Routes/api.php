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

use Modules\Post\Http\Controllers\Api\PostController;

Route::apiResource('post', PostController::class)
    ->names([
        'index' => 'api.post.index',
        'store' => 'api.post.store',
        'show' => 'api.post.show',
        'destroy' => 'api.post.destroy',
        'create' => 'api.post.create',
    ]);
Route::post('post/{id}', [PostController::class, 'update'])->name('api.post.update');
