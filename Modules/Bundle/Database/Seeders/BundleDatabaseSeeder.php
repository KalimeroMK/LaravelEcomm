<?php

declare(strict_types=1);

namespace Modules\Bundle\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;

class BundleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('status', 'active')->take(10)->get();
        
        if ($products->count() < 3) {
            return;
        }

        $bundles = [
            [
                'name' => 'Summer Essentials Bundle',
                'slug' => 'summer-essentials-bundle',
                'description' => 'Get ready for summer with this amazing bundle of essential products.',
                'price' => 99.99,
            ],
            [
                'name' => 'Winter Collection Bundle',
                'slug' => 'winter-collection-bundle',
                'description' => 'Stay warm and stylish with our winter collection bundle.',
                'price' => 149.99,
            ],
            [
                'name' => 'Tech Gadgets Bundle',
                'slug' => 'tech-gadgets-bundle',
                'description' => 'The latest tech gadgets at an unbeatable price.',
                'price' => 299.99,
            ],
        ];

        foreach ($bundles as $bundleData) {
            $bundle = Bundle::create($bundleData);
            // Attach 3-5 random products to each bundle
            $bundle->products()->attach($products->random(rand(3, 5))->pluck('id'));
        }
    }
}
