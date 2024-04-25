<?php

namespace Modules\Cart\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1, 7777),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
        ];
    }

}
