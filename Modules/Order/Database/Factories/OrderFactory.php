<?php

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->unique()->numberBetween(1, 9999),
            'sub_total' => $this->faker->numberBetween(1, 500),
            'total_amount' => $this->faker->numberBetween(1, 500),
            'quantity' => $this->faker->numberBetween(1, 500),
            'created_at' => $this->faker->dateTimeBetween('-365 days', 'now'),
            'updated_at' => Carbon::now(),
            'user_id' => $this->faker->numberBetween(1, 3),
            'shipping_id' => function () {
                return Shipping::factory()->create()->id;
            },
        ];
    }
}
