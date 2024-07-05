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
            'name' => $this->faker->name(),
            'code' => $this->faker->unique()->slug(), // Ensure code is unique
            'type' => $this->faker->randomElement([
                'url',
                'hex',
                'text',
                'date',
                'time',
                'float',
                'string',
                'integer',
                'boolean',
                'decimal',
            ]),
            'display' => $this->faker->randomElement([
                'input',
                'radio',
                'color',
                'button',
                'select',
                'checkbox',
                'multiselect',
            ]),
            'filterable' => $this->faker->numberBetween(0, 1),
            'configurable' => $this->faker->numberBetween(0, 1),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
