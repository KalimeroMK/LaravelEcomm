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
        $type = $this->faker->randomElement(['text', 'boolean', 'date', 'integer', 'float', 'string', 'url']);
        $value = match ($type) {
            'text' => $this->faker->sentence(),
            'boolean' => $this->faker->boolean(),
            'date' => $this->faker->date(),
            'integer' => $this->faker->numberBetween(1, 100),
            'float' => $this->faker->randomFloat(2, 1, 100),
            'string' => $this->faker->word(),
            'url' => $this->faker->url(),
            default => null,
        };

        return [
            'attribute_id' => Attribute::factory(),
            'attributable_id' => Product::factory(),
            'attributable_type' => Product::class,
            'text_value' => $type === 'text' ? $value : null,
            'boolean_value' => $type === 'boolean' ? $value : null,
            'date_value' => $type === 'date' ? $value : null,
            'integer_value' => $type === 'integer' ? $value : null,
            'float_value' => $type === 'float' ? $value : null,
            'string_value' => $type === 'string' ? $value : null,
            'url_value' => $type === 'url' ? $value : null,
        ];
    }

    /**
     * Set the attributable model (polymorphic relation)
     */
    public function forModel(string $modelClass, ?int $id = null): self
    {
        return $this->state(fn (array $attributes) => [
            'attributable_type' => $modelClass,
            'attributable_id' => $id ?? $modelClass::factory(),
        ]);
    }

    /**
     * Create for a Product
     */
    public function forProduct(?Product $product = null): self
    {
        return $this->state(fn (array $attributes) => [
            'attributable_type' => Product::class,
            'attributable_id' => $product !== null ? $product->id : Product::factory(),
        ]);
    }

    /**
     * Set the attribute type
     */
    public function withType(string $type): self
    {
        return $this->state(fn (array $attributes) => [
            'attribute_id' => Attribute::factory()->create(['type' => $type])->id,
        ]);
    }

    /**
     * Set a specific value
     */
    public function withValue(mixed $value): self
    {
        return $this->state(function (array $attributes) use ($value) {
            /** @var Attribute|null $attribute */
            $attribute = Attribute::query()->find($attributes['attribute_id']);
            $type = $attribute instanceof Attribute ? $attribute->type : 'text';

            $column = match ($type) {
                'boolean' => 'boolean_value',
                'date' => 'date_value',
                'integer' => 'integer_value',
                'float' => 'float_value',
                'string' => 'string_value',
                'url' => 'url_value',
                default => 'text_value',
            };

            $result = [
                'text_value' => null,
                'boolean_value' => null,
                'date_value' => null,
                'integer_value' => null,
                'float_value' => null,
                'string_value' => null,
                'url_value' => null,
            ];
            $result[$column] = $value;

            return $result;
        });
    }
}
