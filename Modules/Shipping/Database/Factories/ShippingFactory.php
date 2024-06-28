<?php

namespace Modules\Shipping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Shipping\Models\Shipping;

class ShippingFactory extends Factory
{
    protected $model = Shipping::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->word,
            'price' => $this->faker->numberBetween(1, 100),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
