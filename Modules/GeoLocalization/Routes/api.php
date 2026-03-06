<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\GeoLocalization\Http\Controllers\Api\GeoLocalizationController;

// Public routes
Route::get('geolocation', [GeoLocalizationController::class, 'currentLocation'])->name('api.geolocation.current');
Route::get('geolocation/ip/{ip}', [GeoLocalizationController::class, 'detectIp'])->name('api.geolocation.detect');
Route::get('geolocation/all', [GeoLocalizationController::class, 'all'])->name('api.geolocation.all');

// Currency routes
Route::get('currency', [GeoLocalizationController::class, 'currentCurrency'])->name('api.currency.current');
Route::post('currency', [GeoLocalizationController::class, 'setCurrency'])->name('api.currency.set');
Route::get('currencies', [GeoLocalizationController::class, 'availableCurrencies'])->name('api.currencies.available');
Route::get('exchange-rates', [GeoLocalizationController::class, 'exchangeRates'])->name('api.exchange-rates');
Route::post('currency/convert', [GeoLocalizationController::class, 'convert'])->name('api.currency.convert');
