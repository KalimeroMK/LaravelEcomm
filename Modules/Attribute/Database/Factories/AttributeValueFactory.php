<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Models\Product;

/**
 * @extends Factory<AttributeValue>
 */
class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['text', 'boolean', 'date', 'integer', 'float']);
        $value = match ($type) {
            'text' => $this->faker->sentence(),
            'boolean' => $this->faker->boolean(),
            'date' => $this->faker->date(),
            'integer' => $this->faker->numberBetween(1, 100),
            'float' => $this->faker->randomFloat(2, 1, 100),
            default => null,
        };

        return [
            'product_id' => Product::factory(),
            'attribute_id' => Attribute::factory(),
            'text_value' => $type === 'text' ? $value : null,
            'boolean_value' => $type === 'boolean' ? $value : null,
            'date_value' => $type === 'date' ? $value : null,
            'integer_value' => $type === 'integer' ? $value : null,
            'float_value' => $type === 'float' ? $value : null,
        ];
    }
}
