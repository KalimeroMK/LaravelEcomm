<?php

declare(strict_types=1);

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Tag\Models\Tag;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'summary' => $this->faker->text(),
            'description' => $this->faker->text(),
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'price' => $this->faker->randomFloat(2, 10, 9999),
            'discount' => $this->faker->randomFloat(2, 0, 1000),
            'is_featured' => $this->faker->boolean(),
            'd_deal' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'special_price' => $this->faker->randomFloat(2, 10, 9000),
            'special_price_start' => Carbon::now(),
            'special_price_end' => Carbon::now()->addDays(7),
            'sku' => 'SKU-'.mb_strtoupper(Str::random(10)),
            'brand_id' => Brand::inRandomOrder()->first()->id,
        ];
    }

    /**
     * Configure the factory to create a product with categories.
     */
    public function withCategories(): self
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
    public function withTags(): self
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
    public function withCategoriesAndTags(): self
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

    /**
     * Configure the factory to create a product with attributes and attribute values.
     */
    public function withAttributes(): self
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Product $product */
            $product = $model;
            $attributes = [
                'color' => ['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White'],
                'size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                'material' => ['Silk', 'Cotton', 'Polyester', 'Plastic'],
                'brand' => ['Nike', 'Adidas', 'Puma'],
            ];
            $attributeModels = Attribute::whereIn('code', array_keys($attributes))->get();
            foreach ($attributeModels as $attribute) {
                $value = $attributes[$attribute->code][array_rand($attributes[$attribute->code])];
                $column = method_exists($attribute,
                    'getValueColumnName') ? $attribute->getValueColumnName() : 'text_value';
                if ($column) {
                    $valueData = [
                        'product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        $column => $value,
                    ];
                    AttributeValue::create($valueData);
                }
            }
        });
    }
}
