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

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'bundle_id' => function () {
                /** @var Bundle $bundle */
                $bundle = Bundle::factory()->create();

                return $bundle->id;
            },
            'product_id' => function () {
                /** @var Product $product */
                $product = Product::factory()->create();

                return $product->id;
            },
        ];
    }
}
