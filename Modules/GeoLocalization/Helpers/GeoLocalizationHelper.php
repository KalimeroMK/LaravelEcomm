<?php

declare(strict_types=1);

use Modules\GeoLocalization\DTOs\LocationDTO;
use Modules\GeoLocalization\Services\CurrencyService;
use Modules\GeoLocalization\Services\GeoIpService;

if (! function_exists('geoip_locate')) {
    /**
     * Get location from IP address
     */
    function geoip_locate(?string $ip = null): LocationDTO
    {
        $service = app(GeoIpService::class);
        $ip = $ip ?? $service->getClientIp();
        return $service->locate($ip);
    }
}

if (! function_exists('current_location')) {
    /**
     * Get current user's location
     */
    function current_location(): ?LocationDTO
    {
        return request()->attributes->get('location');
    }
}

if (! function_exists('current_country')) {
    /**
     * Get current country code
     */
    function current_country(): ?string
    {
        return request()->attributes->get('country_code') 
            ?? session('country_code') 
            ?? config('geolocalization.default_country', 'US');
    }
}

if (! function_exists('current_currency')) {
    /**
     * Get current currency code
     */
    function current_currency(): string
    {
        return session('currency', config('geolocalization.default_currency', 'USD'));
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     */
    function currency_symbol(?string $currency = null): string
    {
        $service = app(CurrencyService::class);
        $currency = $currency ?? current_currency();
        return $service->getCurrencySymbol($currency);
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format amount with currency
     */
    function format_currency(float $amount, ?string $currency = null): string
    {
        $service = app(CurrencyService::class);
        return $service->format($amount, $currency);
    }
}

if (! function_exists('convert_currency')) {
    /**
     * Convert amount between currencies
     */
    function convert_currency(float $amount, string $from, string $to): float
    {
        $service = app(CurrencyService::class);
        return $service->convert($amount, $from, $to);
    }
}

if (! function_exists('is_eu_country')) {
    /**
     * Check if current country is in EU
     */
    function is_eu_country(): bool
    {
        $location = current_location();
        return $location?->isEu ?? false;
    }
}

if (! function_exists('detected_timezone')) {
    /**
     * Get detected timezone
     */
    function detected_timezone(): string
    {
        return request()->attributes->get('timezone') 
            ?? session('timezone') 
            ?? config('app.timezone');
    }
}
