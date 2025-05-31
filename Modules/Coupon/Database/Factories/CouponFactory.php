<?php

declare(strict_types=1);

namespace Modules\Coupon\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
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
            'code' => 'code-'.mb_strtoupper(Str::random(10)),
            'value' => $this->faker->randomFloat(),
        ];
    }
}
