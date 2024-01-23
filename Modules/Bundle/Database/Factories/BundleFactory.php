<?php

namespace Modules\Bundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Bundle\Models\Bundle;

class BundleFactory extends Factory
{
    protected $model = Bundle::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
