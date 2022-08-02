<?php

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProduct;
use Modules\Product\Models\Product;

class CategoryProductFactory extends Factory
{
    protected $model = CategoryProduct::class;
    
    #[ArrayShape([
        'created_at'  => "\Illuminate\Support\Carbon",
        'updated_at'  => "\Illuminate\Support\Carbon",
        'product_id'  => "int",
        'category_id' => "int",
    ])] public function definition(): array
    {
        return [
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
            'product_id'  => function () {
                return Product::factory()->create()->id;
            },
            'category_id' => function () {
                return Category::factory()->create()->id;
            },
        ];
    }
}
