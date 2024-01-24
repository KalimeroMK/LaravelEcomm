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
use Modules\User\Http\Controllers\UserController;

Route::prefix('admin')->middleware(['auth',])->group(function () {
    Route::resource('user', UserController::class);
    Route::get('/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/leave-impersonate', [UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');
});
Route::prefix('user')->middleware(['auth',])->group(function () {
    Route::get('user-profile', [UserController::class, 'profile'])->name('user-profile');
    Route::post('/profile/{id}', [UserController::class, 'profileUpdate'])->name('profile-update');
});
