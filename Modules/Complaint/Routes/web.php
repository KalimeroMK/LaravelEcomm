<?php

use Illuminate\Support\Facades\Route;
use Modules\Complaint\Http\Controllers\ComplaintController;

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

Route::resource('complaints', ComplaintController::class)->except('destroy', 'create');
Route::get('complaints/create/{order_id}', [ComplaintController::class, 'create'])->name('complaints.create');