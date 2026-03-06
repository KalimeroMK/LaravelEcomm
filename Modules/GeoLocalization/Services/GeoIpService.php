<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\GeoLocalization\DTOs\LocationDTO;

readonly class GeoIpService
{
    private ?Reader $reader;
    private bool $useApi;
    private string $apiProvider;

    public function __construct()
    {
        $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
        $this->useApi = ! file_exists($dbPath);
        $this->apiProvider = config('geolocalization.provider', 'ipapi');
        
        try {
            $this->reader = $this->useApi ? null : new Reader($dbPath);
        } catch (\Exception $e) {
            $this->reader = null;
            $this->useApi = true;
        }
    }

    /**
     * Get location data from IP address
     */
    public function locate(string $ip): ?LocationDTO
    {
        // Don't track local/private IPs
        if ($this->isPrivateIp($ip)) {
            return $this->getDefaultLocation();
        }

        // Cache location data
        return Cache::remember(
            "geoip:{$ip}",
            config('geolocalization.cache_duration', 3600),
            fn () => $this->performLookup($ip)
        );
    }

    /**
     * Perform the actual IP lookup
     */
    private function performLookup(string $ip): ?LocationDTO
    {
        if ($this->useApi) {
            return $this->lookupViaApi($ip);
        }

        return $this->lookupViaDatabase($ip);
    }

    /**
     * Lookup via local MaxMind database
     */
    private function lookupViaDatabase(string $ip): ?LocationDTO
    {
        if (! $this->reader) {
            return $this->getDefaultLocation();
        }

        try {
            $record = $this->reader->city($ip);

            return new LocationDTO(
                ip: $ip,
                countryCode: $record->country->isoCode ?? null,
                countryName: $record->country->name ?? null,
                city: $record->city->name ?? null,
                region: $record->mostSpecificSubdivision->name ?? null,
                regionCode: $record->mostSpecificSubdivision->isoCode ?? null,
                postalCode: $record->postal->code ?? null,
                latitude: $record->location->latitude ?? null,
                longitude: $record->location->longitude ?? null,
                timezone: $record->location->timeZone ?? null,
                currency: $this->getCurrencyByCountry($record->country->isoCode),
                isEu: $record->country->isInEuropeanUnion ?? false,
                isp: null,
                connectionType: null,
            );
        } catch (AddressNotFoundException) {
            return $this->getDefaultLocation();
        } catch (\Exception $e) {
            logger()->error('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return $this->getDefaultLocation();
        }
    }

    /**
     * Lookup via external API
     */
    private function lookupViaApi(string $ip): ?LocationDTO
    {
        return match ($this->apiProvider) {
            'ipapi' => $this->lookupViaIpApi($ip),
            'ipgeolocation' => $this->lookupViaIpGeolocation($ip),
            default => $this->lookupViaIpApi($ip),
        };
    }

    /**
     * Lookup via ipapi.co (free tier: 45 requests/minute)
     */
    private function lookupViaIpApi(string $ip): ?LocationDTO
    {
        try {
            $response = Http::timeout(5)
                ->get("https://ipapi.co/{$ip}/json/");

            if (! $response->successful()) {
                return $this->getDefaultLocation();
            }

            $data = $response->json();

            if (isset($data['error']) || ! isset($data['country_code'])) {
                return $this->getDefaultLocation();
            }

            return new LocationDTO(
                ip: $ip,
                countryCode: $data['country_code'] ?? null,
                countryName: $data['country_name'] ?? null,
                city: $data['city'] ?? null,
                region: $data['region'] ?? null,
                regionCode: $data['region_code'] ?? null,
                postalCode: $data['postal'] ?? null,
                latitude: $data['latitude'] ?? null,
                longitude: $data['longitude'] ?? null,
                timezone: $data['timezone'] ?? null,
                currency: $data['currency'] ?? $this->getCurrencyByCountry($data['country_code']),
                isEu: $data['in_eu'] ?? false,
                isp: $data['org'] ?? null,
                connectionType: $data['connection_type'] ?? null,
            );
        } catch (\Exception $e) {
            logger()->error('IP API lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return $this->getDefaultLocation();
        }
    }

    /**
     * Lookup via ipgeolocation.io (requires API key)
     */
    private function lookupViaIpGeolocation(string $ip): ?LocationDTO
    {
        $apiKey = config('geolocalization.ipgeolocation_api_key');
        
        if (! $apiKey) {
            return $this->getDefaultLocation();
        }

        try {
            $response = Http::timeout(5)
                ->get('https://api.ipgeolocation.io/ipgeo', [
                    'apiKey' => $apiKey,
                    'ip' => $ip,
                ]);

            if (! $response->successful()) {
                return $this->getDefaultLocation();
            }

            $data = $response->json();

            return new LocationDTO(
                ip: $ip,
                countryCode: $data['country_code2'] ?? null,
                countryName: $data['country_name'] ?? null,
                city: $data['city'] ?? null,
                region: $data['state_prov'] ?? null,
                regionCode: $data['state_code'] ?? null,
                postalCode: $data['zipcode'] ?? null,
                latitude: $data['latitude'] ?? null,
                longitude: $data['longitude'] ?? null,
                timezone: $data['time_zone']['name'] ?? null,
                currency: $data['currency']['code'] ?? null,
                isEu: $data['country_emoji'] === '🇪🇺',
                isp: $data['isp'] ?? null,
                connectionType: $data['connection_type'] ?? null,
            );
        } catch (\Exception $e) {
            logger()->error('IPGeolocation lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return $this->getDefaultLocation();
        }
    }

    /**
     * Check if IP is private/local
     */
    private function isPrivateIp(string $ip): bool
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Get default location (for local/invalid IPs)
     */
    private function getDefaultLocation(): LocationDTO
    {
        return new LocationDTO(
            ip: '127.0.0.1',
            countryCode: config('app.default_country', 'US'),
            countryName: config('app.default_country_name', 'United States'),
            city: null,
            region: null,
            regionCode: null,
            postalCode: null,
            latitude: null,
            longitude: null,
            timezone: config('app.timezone', 'UTC'),
            currency: config('app.default_currency', 'USD'),
            isEu: false,
            isp: null,
            connectionType: null,
        );
    }

    /**
     * Get currency code by country code
     */
    private function getCurrencyByCountry(?string $countryCode): ?string
    {
        if (! $countryCode) {
            return null;
        }

        $map = [
            'US' => 'USD',
            'GB' => 'GBP',
            'EU' => 'EUR',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'NL' => 'EUR',
            'BE' => 'EUR',
            'AT' => 'EUR',
            'JP' => 'JPY',
            'CN' => 'CNY',
            'CA' => 'CAD',
            'AU' => 'AUD',
            'CH' => 'CHF',
            'SE' => 'SEK',
            'NO' => 'NOK',
            'DK' => 'DKK',
            'PL' => 'PLN',
            'CZ' => 'CZK',
            'HU' => 'HUF',
            'RO' => 'RON',
            'BG' => 'BGN',
            'HR' => 'HRK',
            'RS' => 'RSD',
            'MK' => 'MKD',
            'AL' => 'ALL',
            'BA' => 'BAM',
            'SI' => 'EUR',
            'SK' => 'EUR',
            'LT' => 'EUR',
            'LV' => 'EUR',
            'EE' => 'EUR',
            'FI' => 'EUR',
            'IE' => 'EUR',
            'PT' => 'EUR',
            'GR' => 'EUR',
            'MT' => 'EUR',
            'CY' => 'EUR',
            'LU' => 'EUR',
            'IS' => 'ISK',
            'LI' => 'CHF',
            'MC' => 'EUR',
            'SM' => 'EUR',
            'VA' => 'EUR',
            'AD' => 'EUR',
            'ME' => 'EUR',
        ];

        return $map[$countryCode] ?? null;
    }

    /**
     * Get user's IP address from request
     */
    public function getClientIp(): string
    {
        $request = request();
        
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if ($request->server($header)) {
                $ips = explode(',', $request->server($header));
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $request->ip() ?? '127.0.0.1';
    }
}
