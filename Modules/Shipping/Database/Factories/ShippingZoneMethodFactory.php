<?php

declare(strict_types=1);

namespace Modules\Shipping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Models\ShippingZone;
use Modules\Shipping\Models\ShippingZoneMethod;

class ShippingZoneMethodFactory extends Factory
{
    protected $model = ShippingZoneMethod::class;

    public function definition(): array
    {
        return [
            'shipping_zone_id' => ShippingZone::factory(),
            'shipping_id' => Shipping::factory(),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'free_shipping_threshold' => $this->faker->optional()->randomFloat(2, 100, 500),
            'estimated_days' => $this->faker->numberBetween(1, 14),
            'is_active' => true,
            'priority' => $this->faker->numberBetween(0, 10),
        ];
    }
}
