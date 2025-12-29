<?php

declare(strict_types=1);

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

// API Routes
// API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('complaints', ComplaintController::class)->except('destroy', 'create')->names('api.complaints');
    Route::get('complaints/create/{order_id}', [ComplaintController::class, 'create'])->name('api.complaints.create');
});
