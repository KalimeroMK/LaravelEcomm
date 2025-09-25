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
use Modules\Post\Http\Controllers\Api\PostController;

// API Routes
Route::apiResource('posts', PostController::class)
    ->names('api.posts');
Route::post('posts/{id}', [PostController::class, 'update'])->name('api.post.update');
