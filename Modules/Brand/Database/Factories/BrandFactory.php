<?php

declare(strict_types=1);

namespace Modules\Brand\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Brand\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * @return array<string>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->company,
            'slug' => 'slug-'.mb_strtoupper(Str::random(10)),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
