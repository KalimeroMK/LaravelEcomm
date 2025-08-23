<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Brand\Models\Brand;

class Bdka4BrandQuerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create brand 60 for dynamic dataflow without date constraints
        $brand = Brand::updateOrCreate(
            ['id' => 60],
            [
                'title' => 'BRAND60',
                'slug' => 'brand60',
                'status' => 'active',
            ]
        );

        $this->command->info('Brand 60 created/updated for dynamic dataflow (no date range constraints)');
    }
}
