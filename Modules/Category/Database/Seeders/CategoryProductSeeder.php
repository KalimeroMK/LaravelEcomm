<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = Category::factory()->count(10)->create();
        Brand::factory()->count(10)->create();

        Product::factory()
            ->count(10)
            ->hasAttached($categories)
            ->create();
    }
}
