<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Tag\Models\Tag;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique(true)->word,
            'sku' => $this->faker->unique(true)->word,
            'summary' => $this->faker->text,
            'description' => $this->faker->text,
            'condition_id' => $this->faker->numberBetween(1, 2),
            'stock' => 100,
            'price' => $this->faker->numberBetween(1, 9999),
            'discount' => 10,
            'is_featured' => false,
            'brand_id' => $this->faker->numberBetween(1, 10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Configure the factory to create a product with categories.
     */
    public function withCategories(): ProductFactory
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Product $product */
            $product = $model;
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $product->categories()->attach($categories);
        });
    }

    /**
     * Configure the factory to create a product with tags.
     */
    public function withTags(): ProductFactory
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Product $product */
            $product = $model;
            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $product->tags()->attach($tags);
        });
    }

    /**
     * Configure the factory to create a product with categories and tags.
     */
    public function withCategoriesAndTags(): ProductFactory
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Product $product */
            $product = $model;
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $product->categories()->attach($categories);

            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $product->tags()->attach($tags);
        });
    }
}
