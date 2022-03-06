<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Shipping\Models\Shipping;

    class ShippingFactory extends Factory
    {
        protected $model = Shipping::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape([
            'type'       => "string",
            'price'      => "int",
            'created_at' => "\Illuminate\Support\Carbon",
            'updated_at' => "\Illuminate\Support\Carbon",
        ])] public function definition(): array
        {
            return [
                'type'       => $this->faker->word,
                'price'      => $this->faker->numberBetween(1, 100),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
    }
