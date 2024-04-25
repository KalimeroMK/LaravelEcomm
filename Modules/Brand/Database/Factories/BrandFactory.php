<?php

namespace Modules\Brand\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Brand\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * @return array<String>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique(true)->title,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'photo' => $this->faker->imageUrl(640, 480),
        ];
    }
}
