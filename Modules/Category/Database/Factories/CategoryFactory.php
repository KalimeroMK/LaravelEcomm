<?php

declare(strict_types=1);

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        
        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'summary' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement([1, 1, 1, 0]), // 75% active
            'parent_id' => null, // Top level by default
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Create a subcategory
     */
    public function subcategory(?Category $parent = null): self
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent?->id,
            ];
        });
    }

    /**
     * Active category
     */
    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1,
            ];
        });
    }

    /**
     * Inactive category
     */
    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0,
            ];
        });
    }
}
