<?php

namespace Modules\Banner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Banner\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'title'       => "string",
        'photo'       => "string",
        'description' => "string",
        'created_at'  => "\Illuminate\Support\Carbon",
        'updated_at'  => "\Illuminate\Support\Carbon",
    ])] public function definition(): array
    {
        return [
            'title'       => $this->faker->unique()->word,
            'photo'       => $this->faker->word,
            'description' => $this->faker->text,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }
}
