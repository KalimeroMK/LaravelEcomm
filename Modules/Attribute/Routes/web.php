<?php

declare(strict_types=1);

use Modules\Attribute\Http\Controllers\AttributeController;
use Modules\Attribute\Http\Controllers\AttributeGroupController;

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

Route::resource('attributes', AttributeController::class)->except('shows');
Route::resource('attribute_groups', AttributeGroupController::class);
