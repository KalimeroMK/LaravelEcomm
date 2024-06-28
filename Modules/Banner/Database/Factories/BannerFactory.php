<?php

namespace Modules\Banner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Banner\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->word,
            'description' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
