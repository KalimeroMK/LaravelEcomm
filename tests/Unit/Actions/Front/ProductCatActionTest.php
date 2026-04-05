<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Category\Models\Category;
use Modules\Front\Actions\ProductCatAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductCatActionTest extends ActionTestCase
{
    public function test_invoke_returns_category_with_children(): void
    {
        // Arrange
        $parent = Category::factory()->create([
            'slug' => 'electronics',
            'status' => 'active',
        ]);
        $child = Category::factory()->create([
            'parent_id' => $parent->id,
            'status' => 'active',
        ]);

        $action = app(ProductCatAction::class);

        // Act
        $result = $action('electronics');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('childCategories', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('recentProducts', $result);
        $this->assertEquals($parent->id, $result['category']->id);
    }

    public function test_invoke_returns_products_for_leaf_category(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'slug' => 'phones',
            'status' => 'active',
        ]);
        $product = Product::factory()->create(['status' => 'active']);
        $category->products()->attach($product->id);

        $action = app(ProductCatAction::class);

        // Act
        $result = $action('phones');

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }

    public function test_invoke_returns_empty_products_for_parent_category(): void
    {
        // Arrange
        $parent = Category::factory()->create([
            'slug' => 'parent-cat',
            'status' => 'active',
        ]);
        Category::factory()->create([
            'parent_id' => $parent->id,
            'status' => 'active',
        ]);

        $action = app(ProductCatAction::class);

        // Act
        $result = $action('parent-cat');

        // Assert
        $this->assertTrue($result['products']->isEmpty());
    }

    public function test_invoke_returns_error_for_nonexistent_category(): void
    {
        // Arrange
        $action = app(ProductCatAction::class);

        // Act
        $result = $action('nonexistent-category');

        // Assert
        $this->assertIsArray($result);
        $this->assertNull($result['category']);
        $this->assertEquals('Category not found', $result['error']);
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'slug' => 'test-cat',
            'status' => 'active',
        ]);
        Product::factory()->count(5)->create(['status' => 'active']);

        $action = app(ProductCatAction::class);

        // Act
        $result = $action('test-cat');

        // Assert
        $this->assertLessThanOrEqual(4, $result['recentProducts']->count());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Category::factory()->create([
            'slug' => 'cached-cat',
            'status' => 'active',
        ]);

        $action = app(ProductCatAction::class);

        // Act
        $result1 = $action('cached-cat');
        $result2 = $action('cached-cat');

        // Assert
        $this->assertEquals($result1['category']->id, $result2['category']->id);
    }
}
