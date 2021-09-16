<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;

    class CategoryProductFactory extends Factory
    {
        protected $model = CategoryProductFactory::class;

        public function definition(): array
        {
            return [
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
                'product_id'  => $this->faker->numberBetween(1, 500),
                'category_id' => $this->faker->numberBetween(1, 10),
            ];
        }
    }
