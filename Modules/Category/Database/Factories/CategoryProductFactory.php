<?php

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProduct;
use Modules\Product\Models\Product;

class CategoryProductFactory extends Factory
{
    protected $model = CategoryProduct::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'product_id' => function () {
                /** @var Product $product */
                $product = Product::factory()->create();

                return $product->id;
            },
            'category_id' => function () {
                /** @var Category $category */
                $category = Category::factory()->create();

                return $category->id;
            },
        ];
    }
}
