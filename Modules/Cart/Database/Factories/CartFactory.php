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
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'product_id' => function () {
                return Product::factory()->create()->id;
            },
            'order_id' => null, // Don't create order for every cart
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'session_id' => $this->faker->uuid(),
            'status' => 'new',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
