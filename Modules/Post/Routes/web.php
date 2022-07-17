<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Post\Http\Controllers\PostCommentController;
use Modules\Post\Http\Controllers\PostController;

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('/posts', PostController::class);
});
Route::post('post/{slug}/comment', [PostCommentController::class, 'store'])->name('post-comment.store');
Route::resource('/post_comments', PostCommentController::class);
