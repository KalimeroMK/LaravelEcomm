<?php

use Illuminate\Support\Facades\Route;
use Modules\Bundle\Http\Controllers\Api\BundleController;

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

Route::apiResource('bundles', BundleController::class)
    ->except('update')
    ->names([
        'index' => 'api.bundle.index',
        'store' => 'api.bundle.store',
        'show' => 'api.bundle.show',
        'destroy' => 'api.bundle.destroy',
        'create' => 'api.bundle.create', // if you're using forms for APIs
    ]);
Route::delete('/bundles/{modelId}/media/{mediaId}',
    [BundleController::class, 'deleteMedia'])->name('api.bundle.delete-media');
