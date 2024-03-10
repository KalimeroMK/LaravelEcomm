<?php

namespace Modules\Bundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Models\BundleProduct;
use Modules\Product\Models\Product;

class BundleProductFactory extends Factory
{
    protected $model = BundleProduct::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'bundle_id' => function () {
                return Bundle::factory()->create()->id;
            },
            'product_id' => function () {
                return Product::factory()->create()->id;
            },
        ];
    }
}
