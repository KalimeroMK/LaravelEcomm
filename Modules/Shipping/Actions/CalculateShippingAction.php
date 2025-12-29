<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\Models\ShippingZone;
use Modules\Shipping\Models\ShippingZoneMethod;

readonly class CalculateShippingAction
{
    /**
     * Calculate shipping cost based on location and order total
     */
    public function execute(
        ?string $country = null,
        ?string $region = null,
        ?string $postalCode = null,
        float $orderTotal = 0.0
    ): array {
        // Find matching shipping zones
        $zones = ShippingZone::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $availableMethods = [];

        foreach ($zones as $zone) {
            if ($zone->matchesLocation($country, $region, $postalCode)) {
                $methods = ShippingZoneMethod::where('shipping_zone_id', $zone->id)
                    ->where('is_active', true)
                    ->with('shipping')
                    ->orderBy('priority', 'desc')
                    ->get();

                foreach ($methods as $method) {
                    $cost = $method->calculateCost($orderTotal);
                    $availableMethods[] = [
                        'id' => $method->shipping_id,
                        'zone_method_id' => $method->id,
                        'name' => $method->shipping->type ?? 'Unknown',
                        'price' => $cost,
                        'original_price' => $method->price,
                        'estimated_days' => $method->estimated_days,
                        'is_free' => $cost === 0,
                    ];
                }

                // If we found methods in a zone, stop checking other zones
                if (! empty($availableMethods)) {
                    break;
                }
            }
        }

        // If no zone methods found, fall back to default shipping methods
        if (empty($availableMethods)) {
            $defaultShipping = \Modules\Shipping\Models\Shipping::where('status', 'active')->get();
            foreach ($defaultShipping as $shipping) {
                $availableMethods[] = [
                    'id' => $shipping->id,
                    'zone_method_id' => null,
                    'name' => $shipping->type,
                    'price' => (float) $shipping->price,
                    'original_price' => (float) $shipping->price,
                    'estimated_days' => null,
                    'is_free' => false,
                ];
            }
        }

        return [
            'methods' => $availableMethods,
            'total' => count($availableMethods),
        ];
    }
}
