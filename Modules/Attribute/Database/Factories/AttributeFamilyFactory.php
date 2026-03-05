<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Attribute\Models\AttributeFamily;

/**
 * @extends Factory<AttributeFamily>
 */
class AttributeFamilyFactory extends Factory
{
    protected $model = AttributeFamily::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => $name,
            'code' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Set the family as inactive
     */
    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set specific code
     */
    public function withCode(string $code): self
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }

    /**
     * Attach attributes to the family
     */
    public function withAttributes(int $count = 3): self
    {
        return $this->afterCreating(function (AttributeFamily $family) use ($count) {
            $attributes = \Modules\Attribute\Models\Attribute::factory()->count($count)->create();
            $family->attributes()->attach($attributes->pluck('id'));
        });
    }

    /**
     * Attach specific attributes to the family
     */
    public function withSpecificAttributes(array $attributeIds): self
    {
        return $this->afterCreating(function (AttributeFamily $family) use ($attributeIds) {
            $family->attributes()->attach($attributeIds);
        });
    }
}
