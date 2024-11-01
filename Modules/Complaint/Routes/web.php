<?php

use Illuminate\Support\Facades\Route;
use Modules\Complaint\Http\Controllers\ComplaintController;
use Modules\Complaint\Http\Controllers\ComplaintReplyController;

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

Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
Route::get('/complaints/{complaint}', [ComplaintController::class, 'edit'])->name('complaints.edit');
Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.create');
Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
Route::post('/complaints/{complaint}/replies', [ComplaintReplyController::class, 'store'])->name(
    'complaints.replies.store'
);
Route::get('/complaints/{complaint}/replies', [ComplaintReplyController::class, 'index']);