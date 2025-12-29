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
Route::delete('posts/{modelId}/media/{mediaId}', [PostController::class, 'deleteMedia'])->name('api.posts.delete-media');
Route::post('posts/import', [PostController::class, 'import'])->name('api.posts.import');
