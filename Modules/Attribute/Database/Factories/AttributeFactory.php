<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Attribute\Models\Attribute;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
            'code' => 'code-'.mb_strtoupper(Str::random(10)),
            'type' => $this->faker->randomElement(['text', 'boolean', 'date', 'integer', 'float', 'select']),
            'display' => $this->faker->randomElement(['input', 'select', 'checkbox', 'radio']),
            'is_filterable' => $this->faker->boolean(),
            'is_configurable' => $this->faker->boolean(),

        ];
    }
}
