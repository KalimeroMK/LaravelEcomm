<?php

declare(strict_types=1);

namespace Modules\Cart\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(),
            'quantity' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'product_id' => function () {
                return Product::factory()->create()->id;
            },
            'order_id' => function () {
                return Order::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ];
    }
}
