<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Attribute\Models\Attribute;

/**
 * @extends Factory<Attribute>
 */
class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
            'code' => 'code-'.mb_strtoupper(Str::random(10)),
            'type' => $this->faker->randomElement(['url', 'hex', 'text', 'date', 'time', 'float', 'integer', 'boolean', 'decimal', 'string']),
            'display' => $this->faker->randomElement(['input', 'radio', 'color', 'button', 'select', 'checkbox', 'multiselect']),
            'is_filterable' => $this->faker->numberBetween(0, 1),
            'is_configurable' => $this->faker->numberBetween(0, 1),

        ];
    }
}
