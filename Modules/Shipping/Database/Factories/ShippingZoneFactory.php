<?php

declare(strict_types=1);

namespace Modules\Shipping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shipping\Models\ShippingZone;

class ShippingZoneFactory extends Factory
{
    protected $model = ShippingZone::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Zone',
            'description' => $this->faker->sentence,
            'countries' => [$this->faker->countryCode, $this->faker->countryCode],
            'regions' => null,
            'postal_codes' => null,
            'is_active' => true,
            'priority' => $this->faker->numberBetween(0, 10),
        ];
    }
}
