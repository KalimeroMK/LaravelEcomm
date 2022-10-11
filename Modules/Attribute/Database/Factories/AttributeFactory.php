<?php

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Attribute\Models\Attribute;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;
    
    public function definition(): array
    {
        return [
            'name'         => $this->faker->name(),
            'code'         => $this->faker->unique()->name,
            'type'         => $this->faker->word(),
            'display'      => $this->faker->word(),
            'filterable'   => $this->faker->boolean(),
            'configurable' => $this->faker->boolean(),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
    }
}
