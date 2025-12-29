<?php

declare(strict_types=1);

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Core;
use Modules\Shipping\Database\Factories\ShippingZoneFactory;

class ShippingZone extends Core
{
    use HasFactory;

    protected $table = 'shipping_zones';

    protected $fillable = [
        'name',
        'description',
        'countries',
        'regions',
        'postal_codes',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'countries' => 'array',
        'regions' => 'array',
        'postal_codes' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function methods(): HasMany
    {
        return $this->hasMany(ShippingZoneMethod::class);
    }

    /**
     * Check if a location matches this zone
     */
    public function matchesLocation(?string $country = null, ?string $region = null, ?string $postalCode = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Check countries
        if ($country && $this->countries) {
            if (! in_array($country, $this->countries)) {
                return false;
            }
        }

        // Check regions
        if ($region && $this->regions) {
            if (! in_array($region, $this->regions)) {
                return false;
            }
        }

        // Check postal codes
        if ($postalCode && $this->postal_codes) {
            $matches = false;
            foreach ($this->postal_codes as $range) {
                if (is_array($range) && isset($range['from']) && isset($range['to'])) {
                    if ($postalCode >= $range['from'] && $postalCode <= $range['to']) {
                        $matches = true;
                        break;
                    }
                } elseif ($postalCode === $range) {
                    $matches = true;
                    break;
                }
            }
            if (! $matches) {
                return false;
            }
        }

        return true;
    }

    protected static function newFactory(): ShippingZoneFactory
    {
        return ShippingZoneFactory::new();
    }
}
