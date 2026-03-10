<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Front\Actions\ProductSearchAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductSearchActionTest extends ActionTestCase
{
    public function test_invoke_returns_search_results(): void
    {
        // Arrange
        Product::factory()->count(5)->create(['status' => 'active']);
        Brand::factory()->count(3)->create(['status' => 'active']);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'product']);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('recent_products', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('brands', $result);
    }

    public function test_invoke_searches_by_title(): void
    {
        // Arrange
        Product::factory()->create([
            'title' => 'iPhone 15 Pro',
            'status' => 'active',
        ]);
        Product::factory()->create([
            'title' => 'Samsung Galaxy',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'iPhone']);

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }

    public function test_invoke_searches_by_description(): void
    {
        // Arrange
        Product::factory()->create([
            'title' => 'Product A',
            'description' => 'This is an amazing laptop with great features',
            'status' => 'active',
        ]);
        Product::factory()->create([
            'title' => 'Product B',
            'description' => 'A simple phone case',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'laptop']);

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }

    public function test_invoke_searches_brands(): void
    {
        // Arrange
        Brand::factory()->create([
            'title' => 'Nike',
            'status' => 'active',
        ]);
        Brand::factory()->create([
            'title' => 'Adidas',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'Nike']);

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['brands']->count());
    }

    public function test_invoke_returns_empty_for_no_matches(): void
    {
        // Arrange
        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'xyznonexistent123']);

        // Assert
        $this->assertEquals(0, $result['products']->total());
        $this->assertEquals(0, $result['brands']->count());
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create(['status' => 'active']);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'test']);

        // Assert
        $this->assertCount(3, $result['recent_products']);
    }

    public function test_invoke_only_returns_active_products_in_results(): void
    {
        // Arrange - Create products with specific searchable titles
        Product::factory()->create([
            'title' => 'Active Test Product ABC',
            'status' => 'active',
        ]);
        Product::factory()->create([
            'title' => 'Inactive Test Product ABC',
            'status' => 'inactive',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'ABC']);

        // Assert
        $this->assertGreaterThan(0, $result['products']->total(), 'Should have some products to verify');
        foreach ($result['products'] as $product) {
            $this->assertEquals('active', $product->status);
        }
    }

    public function test_invoke_respects_per_page_parameter(): void
    {
        // Arrange
        Product::factory()->count(15)->create([
            'title' => 'Test Product',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'Test', 'per_page' => 12]);

        // Assert
        $this->assertLessThanOrEqual(12, $result['products']->count());
    }

    public function test_invoke_uses_default_per_page(): void
    {
        // Arrange
        Product::factory()->count(15)->create([
            'title' => 'Paginated Product',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'Paginated']);

        // Assert
        $this->assertLessThanOrEqual(9, $result['products']->count());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Product::factory()->create([
            'title' => 'Cached Product',
            'status' => 'active',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result1 = $action(['search' => 'Cached']);
        $result2 = $action(['search' => 'Cached']);

        // Assert
        $this->assertEquals($result1['products']->pluck('id'), $result2['products']->pluck('id'));
    }

    public function test_invoke_only_returns_active_brands(): void
    {
        // Arrange
        Brand::factory()->create([
            'title' => 'Active Brand',
            'status' => 'active',
        ]);
        Brand::factory()->create([
            'title' => 'Inactive Brand',
            'status' => 'inactive',
        ]);

        $action = app(ProductSearchAction::class);

        // Act
        $result = $action(['search' => 'Brand']);

        // Assert
        foreach ($result['brands'] as $brand) {
            $this->assertEquals('active', $brand->status);
        }
    }
}
