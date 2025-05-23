<?php

declare(strict_types=1);

namespace Modules\Banner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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
            'title' => 'title'.mb_strtoupper(Str::random(10)),
            'slug' => 'slug'.mb_strtoupper(Str::random(10)),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'description' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
