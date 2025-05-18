<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->slug(2),
            'type' => $this->faker->randomElement(['text', 'boolean', 'date', 'integer', 'float', 'select']),
            'display' => $this->faker->randomElement(['input', 'select', 'checkbox', 'radio']),
            'is_filterable' => $this->faker->boolean(),
            'is_configurable' => $this->faker->boolean(),
            'attribute_group_id' => AttributeGroup::factory(),
        ];
    }
}
