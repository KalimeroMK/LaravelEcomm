<?php

use Illuminate\Support\Facades\Route;
use Modules\Complaint\Http\Controllers\Api\ComplaintController;

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

Route::resource('complaints', ComplaintController::class)->except('destroy', 'create');
Route::get('complaints/create/{order_id}', [ComplaintController::class, 'create'])->name('complaints.create');
