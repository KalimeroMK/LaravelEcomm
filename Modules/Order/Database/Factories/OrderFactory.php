<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => $this->faker->word(),
            'sub_total' => $this->faker->randomFloat(),
            'total_amount' => $this->faker->randomFloat(),
            'quantity' => $this->faker->randomNumber(),
            'payment_method' => $this->faker->randomElement(['cod', 'paypal']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'unpaid']),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'transaction_reference' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'payer_id' => fn () => User::factory()->create()->id,
            'user_id' => fn () => User::factory()->create()->id,
            'shipping_id' => fn () => Shipping::factory()->create()->id,

        ];
    }
}
