<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Coupon\Models\Coupon;

    class CouponFactory extends Factory
    {
        protected $model = Coupon::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape(['code' => "string", 'value' => "float"])] public function definition(): array
        {
            return [
                'code'  => $this->faker->word,
                'value' => $this->faker->randomFloat(),
            ];
        }
    }
