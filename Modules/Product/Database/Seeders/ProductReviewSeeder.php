<?php

namespace Modules\Product\Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductReview;

class ProductReviewSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(): void
    {
        for ($i = 0; $i < 300; $i++) {
            ProductReview::create([
                'rate' => random_int(1, 5),
                'review' => Str::random(40),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => random_int(1, 3),
                'product_id' => random_int(1, 200),
            ]);
        }
    }
}
