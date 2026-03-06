<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    private string $baseCurrency;
    private ?array $rates = null;
    private string $provider;

    public function __construct()
    {
        $this->baseCurrency = config('geolocalization.base_currency', 'USD');
        $this->provider = config('geolocalization.currency_provider', 'exchangerate-api');
    }

    /**
     * Get exchange rates
     *
     * @return array<string, float>
     */
    public function getRates(): array
    {
        if ($this->rates !== null) {
            return $this->rates;
        }

        return Cache::remember(
            'currency_rates',
            config('geolocalization.currency_cache_duration', 3600),
            fn () => $this->fetchRates()
        );
    }

    /**
     * Fetch rates from API
     *
     * @return array<string, float>
     */
    private function fetchRates(): array
    {
        return match ($this->provider) {
            'exchangerate-api' => $this->fetchFromExchangeRateApi(),
            'openexchangerates' => $this->fetchFromOpenExchangeRates(),
            'fixer' => $this->fetchFromFixer(),
            default => $this->getDefaultRates(),
        };
    }

    /**
     * Fetch from ExchangeRate-API (free tier available)
     */
    private function fetchFromExchangeRateApi(): array
    {
        $apiKey = config('geolocalization.exchangerate_api_key');
        
        if (! $apiKey) {
            return $this->getDefaultRates();
        }

        try {
            $response = Http::timeout(10)
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$this->baseCurrency}");

            if (! $response->successful()) {
                return $this->getDefaultRates();
            }

            $data = $response->json();

            return $data['conversion_rates'] ?? $this->getDefaultRates();
        } catch (\Exception $e) {
            logger()->error('ExchangeRate API failed', ['error' => $e->getMessage()]);
            return $this->getDefaultRates();
        }
    }

    /**
     * Fetch from Open Exchange Rates
     */
    private function fetchFromOpenExchangeRates(): array
    {
        $apiKey = config('geolocalization.openexchangerates_api_key');
        
        if (! $apiKey) {
            return $this->getDefaultRates();
        }

        try {
            $response = Http::timeout(10)
                ->get('https://openexchangerates.org/api/latest.json', [
                    'app_id' => $apiKey,
                    'base' => $this->baseCurrency,
                ]);

            if (! $response->successful()) {
                return $this->getDefaultRates();
            }

            return $response->json()['rates'] ?? $this->getDefaultRates();
        } catch (\Exception $e) {
            logger()->error('OpenExchangeRates API failed', ['error' => $e->getMessage()]);
            return $this->getDefaultRates();
        }
    }

    /**
     * Fetch from Fixer.io
     */
    private function fetchFromFixer(): array
    {
        $apiKey = config('geolocalization.fixer_api_key');
        
        if (! $apiKey) {
            return $this->getDefaultRates();
        }

        try {
            $response = Http::timeout(10)
                ->get('https://data.fixer.io/api/latest', [
                    'access_key' => $apiKey,
                    'base' => $this->baseCurrency,
                ]);

            if (! $response->successful()) {
                return $this->getDefaultRates();
            }

            return $response->json()['rates'] ?? $this->getDefaultRates();
        } catch (\Exception $e) {
            logger()->error('Fixer API failed', ['error' => $e->getMessage()]);
            return $this->getDefaultRates();
        }
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getRates();

        // Convert to base currency first
        if ($from !== $this->baseCurrency) {
            $fromRate = $rates[$from] ?? 1;
            $amount = $amount / $fromRate;
        }

        // Convert from base to target
        $toRate = $rates[$to] ?? 1;
        return $amount * $toRate;
    }

    /**
     * Get current session currency
     */
    public function getCurrentCurrency(): string
    {
        return session('currency', config('app.default_currency', 'USD'));
    }

    /**
     * Set current session currency
     */
    public function setCurrency(string $currency): void
    {
        session()->put('currency', strtoupper($currency));
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? $this->getCurrentCurrency();
        $symbol = $this->getCurrencySymbol($currency);
        
        return $symbol . number_format($amount, 2);
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'CZK' => 'Kč',
            'HUF' => 'Ft',
            'RON' => 'lei',
            'BGN' => 'лв',
            'HRK' => 'kn',
            'RSD' => 'din',
            'MKD' => 'ден',
            'ALL' => 'L',
            'BAM' => 'KM',
            'ISK' => 'kr',
        ];

        return $symbols[$currency] ?? $currency . ' ';
    }

    /**
     * Get available currencies
     *
     * @return array<string, string>
     */
    public function getAvailableCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CNY' => 'Chinese Yuan',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'CHF' => 'Swiss Franc',
            'SEK' => 'Swedish Krona',
            'NOK' => 'Norwegian Krone',
            'DKK' => 'Danish Krone',
            'PLN' => 'Polish Złoty',
            'CZK' => 'Czech Koruna',
            'HUF' => 'Hungarian Forint',
            'RON' => 'Romanian Leu',
            'BGN' => 'Bulgarian Lev',
            'MKD' => 'Macedonian Denar',
            'RSD' => 'Serbian Dinar',
            'ALL' => 'Albanian Lek',
            'BAM' => 'Bosnia Mark',
            'HRK' => 'Croatian Kuna',
        ];
    }

    /**
     * Default rates as fallback
     *
     * @return array<string, float>
     */
    private function getDefaultRates(): array
    {
        return [
            'USD' => 1.0,
            'EUR' => 0.85,
            'GBP' => 0.73,
            'JPY' => 110.0,
            'CNY' => 6.45,
            'CAD' => 1.25,
            'AUD' => 1.35,
            'CHF' => 0.92,
            'SEK' => 8.5,
            'NOK' => 8.8,
            'DKK' => 6.3,
            'PLN' => 3.85,
            'CZK' => 21.5,
            'HUF' => 300.0,
            'RON' => 4.2,
            'BGN' => 1.66,
            'MKD' => 51.5,
            'RSD' => 99.0,
            'ALL' => 103.0,
            'BAM' => 1.66,
            'HRK' => 6.35,
        ];
    }
}
