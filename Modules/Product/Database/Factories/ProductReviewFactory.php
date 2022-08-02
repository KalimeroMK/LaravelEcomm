<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Product\Models\ProductReview;

class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;
    
    public function definition(): array
    {
        return [
            'rate'       => $this->faker->numberBetween(1, 5),
            'review'     => $this->faker->word(),
            'status'     => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id'    => $this->faker->numberBetween(1, 3),
            'product_id' => $this->faker->numberBetween(1, 200),
        ];
    }
}
