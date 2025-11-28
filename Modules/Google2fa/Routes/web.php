<?php

declare(strict_types=1);

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

use Modules\Google2fa\Http\Controllers\Google2faController;

Route::get('/2fa', [Google2faController::class, 'show2faForm'])->name('admin.2fa');
Route::post('/google-generateSecret', [Google2faController::class, 'generate2faSecret'])->name('admin.generate2faSecret');
Route::post('/google-enable2fa', [Google2faController::class, 'enable2fa'])->name('admin.enable2fa');
Route::get('/google-disable2fa', [Google2faController::class, 'disable2fa'])->name('admin.google-disable2fa');
Route::post('/google-2faVerify', [Google2faController::class, 'verify2fa'])->name('admin.2faVerify');
