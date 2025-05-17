<?php

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Models\AttributeGroup;

class AttributeGroupFactory extends Factory
{
    protected $model = AttributeGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
