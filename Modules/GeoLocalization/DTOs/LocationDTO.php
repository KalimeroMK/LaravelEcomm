<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\DTOs;

readonly class LocationDTO
{
    public function __construct(
        public string $ip,
        public ?string $countryCode,
        public ?string $countryName,
        public ?string $city,
        public ?string $region,
        public ?string $regionCode,
        public ?string $postalCode,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $timezone,
        public ?string $currency,
        public bool $isEu,
        public ?string $isp,
        public ?string $connectionType,
    ) {}

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ip' => $this->ip,
            'country_code' => $this->countryCode,
            'country_name' => $this->countryName,
            'city' => $this->city,
            'region' => $this->region,
            'region_code' => $this->regionCode,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'is_eu' => $this->isEu,
            'isp' => $this->isp,
            'connection_type' => $this->connectionType,
        ];
    }

    /**
     * Get formatted location string
     */
    public function getFormattedLocation(): string
    {
        $parts = array_filter([
            $this->city,
            $this->region,
            $this->countryName,
        ]);

        return implode(', ', $parts) ?: 'Unknown';
    }

    /**
     * Check if location is in EU
     */
    public function isInEu(): bool
    {
        return $this->isEu;
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(): string
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

        return $symbols[$this->currency] ?? $this->currency ?? '$';
    }
}
