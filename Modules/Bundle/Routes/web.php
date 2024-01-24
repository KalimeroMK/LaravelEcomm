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

use Modules\Bundle\Http\Controllers\BundleController;

Route::resource('bundle', BundleController::class);
Route::delete('/bundle/{modelId}/media/{mediaId}',
    [BundleController::class, 'deleteMedia'])->name('bundle.delete-media');
