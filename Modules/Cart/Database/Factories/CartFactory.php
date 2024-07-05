<?php

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
                /** @var Product $product */
                $product = Product::factory()->create();

                return $product->id;
            },
            'order_id' => function () {
                /** @var Order $order */
                $order = Order::factory()->create();

                return $order->id;
            },
            // Other definitions
            'user_id' => function () {
                /** @var User $user */
                $user = User::factory()->create();

                return $user->id;
            },
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ];
    }
}
