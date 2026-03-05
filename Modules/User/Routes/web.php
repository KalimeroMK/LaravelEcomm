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

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\UserAddressController;

Route::prefix('admin')->middleware(['auth'])->group(function (): void {
    Route::resource('users', UserController::class);
    Route::get('/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/leave-impersonate', [UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');
});
Route::prefix('user')->middleware(['auth'])->group(function (): void {
    Route::get('user-profile', [UserController::class, 'profile'])->name('user-profile');
    Route::post('/profile/{id}', [UserController::class, 'profileUpdate'])->name('profile-update');
    
    // Address Book Routes
    Route::get('addresses', [UserAddressController::class, 'index'])->name('user.addresses.index');
    Route::get('addresses/create', [UserAddressController::class, 'create'])->name('user.addresses.create');
    Route::post('addresses', [UserAddressController::class, 'store'])->name('user.addresses.store');
    Route::get('addresses/{address}/edit', [UserAddressController::class, 'edit'])->name('user.addresses.edit');
    Route::put('addresses/{address}', [UserAddressController::class, 'update'])->name('user.addresses.update');
    Route::delete('addresses/{address}', [UserAddressController::class, 'destroy'])->name('user.addresses.destroy');
    Route::post('addresses/{address}/default', [UserAddressController::class, 'setDefault'])->name('user.addresses.default');
});
