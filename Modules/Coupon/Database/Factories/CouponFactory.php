<?php

namespace Modules\Coupon\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Coupon\Models\Coupon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
        ];
    }
}
