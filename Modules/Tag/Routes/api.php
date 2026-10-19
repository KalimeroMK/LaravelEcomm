<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
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

// API Routes
//
// Named explicitly: the bare resource names collide with the admin `tags.*`
// web routes in Routes/web.php, which both breaks route:cache and makes
// route('tags.index') resolve to the API endpoint instead of the admin screen.
Route::apiResource('tag', TagController::class)->names('api.tag');
Route::apiResource('tags', TagController::class)->names('api.tags');
