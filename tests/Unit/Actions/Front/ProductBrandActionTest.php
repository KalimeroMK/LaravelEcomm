<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Front\Actions\ProductBrandAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductBrandActionTest extends ActionTestCase
{
    public function test_invoke_returns_products_by_brand(): void
    {
        // Arrange
        $brand = Brand::factory()->create(['slug' => 'nike', 'status' => 'active']);
        $product = Product::factory()->create([
            'status' => 'active',
            'brand_id' => $brand->id,
        ]);

        $action = app(ProductBrandAction::class);

        // Act
        $result = $action('nike');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('brands', $result);
        $this->assertArrayHasKey('recentProducts', $result);
    }

    public function test_invoke_returns_products_with_brand_relation(): void
    {
        // Arrange
        $brand = Brand::factory()->create(['slug' => 'adidas', 'status' => 'active']);
        Product::factory()->count(3)->create([
            'status' => 'active',
            'brand_id' => $brand->id,
        ]);

        $action = app(ProductBrandAction::class);

        // Act
        $result = $action('adidas');

        // Assert
        $this->assertGreaterThanOrEqual(3, $result['products']->total());
    }

    public function test_invoke_returns_all_active_brands(): void
    {
        // Arrange
        Brand::factory()->count(5)->create(['status' => 'active']);
        Brand::factory()->count(2)->create(['status' => 'inactive']);

        $brand = Brand::factory()->create(['slug' => 'test-brand', 'status' => 'active']);
        Product::factory()->create(['brand_id' => $brand->id, 'status' => 'active']);

        $action = app(ProductBrandAction::class);

        // Act
        $result = $action('test-brand');

        // Assert
        $this->assertCount(6, $result['brands']); // 5 + test-brand
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        $brand = Brand::factory()->create(['slug' => 'puma', 'status' => 'active']);
        Product::factory()->count(5)->create(['status' => 'active']);
        Product::factory()->create(['brand_id' => $brand->id, 'status' => 'active']);

        $action = app(ProductBrandAction::class);

        // Act
        $result = $action('puma');

        // Assert
        $this->assertCount(3, $result['recentProducts']);
    }

    public function test_invoke_returns_empty_for_nonexistent_brand(): void
    {
        // Arrange
        $action = app(ProductBrandAction::class);

        // Act
        $result = $action('nonexistent-brand');

        // Assert
        $this->assertEquals(0, $result['products']->total());
    }
}
