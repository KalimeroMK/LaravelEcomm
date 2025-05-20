<?php

declare(strict_types=1);

use Modules\Attribute\Http\Controllers\Api\AttributeController;
use Modules\Attribute\Http\Controllers\Api\AttributeGroupController;

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

Route::apiResource('attributes', AttributeController::class)->names('api.attribute.index');
Route::apiResource('attribute-groups', AttributeGroupController::class);
