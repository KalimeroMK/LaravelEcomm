<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Product\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique(true)->word,
            'summary' => $this->faker->text,
            'description' => $this->faker->text,
            'condition_id' => $this->faker->numberBetween(1, 2),
            'stock' => 100,
            'price' => $this->faker->numberBetween(1, 9999),
            'discount' => 10,
            'is_featured' => false,
            'brand_id' => $this->faker->numberBetween(1, 10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
