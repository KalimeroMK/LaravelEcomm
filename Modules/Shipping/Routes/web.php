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

use Modules\Shipping\Http\Controllers\ShippingController;
use Modules\Shipping\Http\Controllers\ShippingZoneController;

Route::resource('shipping', ShippingController::class)->names([
    'index' => 'admin.shipping.index',
    'create' => 'admin.shipping.create',
    'store' => 'admin.shipping.store',
    'show' => 'admin.shipping.show',
    'edit' => 'admin.shipping.edit',
    'update' => 'admin.shipping.update',
    'destroy' => 'admin.shipping.destroy',
]);

Route::prefix('shipping')->group(function () {
    Route::resource('zones', ShippingZoneController::class)->names([
        'index' => 'shipping.zones.index',
        'create' => 'shipping.zones.create',
        'store' => 'shipping.zones.store',
        'edit' => 'shipping.zones.edit',
        'update' => 'shipping.zones.update',
        'destroy' => 'shipping.zones.destroy',
    ])->parameters(['zones' => 'zone']);
});
