<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Cart\Models\Cart;
    use Modules\Order\Models\Order;
    use Modules\Product\Models\Product;

    class CartFactory extends Factory
    {
        protected $model = Cart::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape([
            'user_id'    => "int",
            'price'      => "int",
            'quantity'   => "int",
            'amount'     => "int",
            'created_at' => "\Illuminate\Support\Carbon",
            'updated_at' => "\Illuminate\Support\Carbon",
            'product_id' => "int",
            'order_id'   => "\Closure",
        ])] public function definition(): array
        {
            return [
                'user_id'    => $this->faker->numberBetween(1, 3),
                'price'      => $this->faker->numberBetween(1, 7777),
                'quantity'   => $this->faker->numberBetween(1, 7777),
                'amount'     => $this->faker->numberBetween(1, 7777),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_id' => function () {
                    return Product::factory()->create()->id;
                },
                'order_id'   => function () {
                    return Order::factory()->create()->id;
                },
            ];
        }
    }
