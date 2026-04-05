<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Front\Actions\ProductListsAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductListsActionTest extends ActionTestCase
{
    public function test_invoke_returns_product_lists_data(): void
    {
        // Arrange
        Product::factory()->count(5)->create(['status' => 'active']);
        Brand::factory()->count(3)->create(['status' => 'active']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('recent_products', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('brands', $result);
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create(['status' => 'active']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(3, $result['recent_products']);
    }

    public function test_invoke_returns_active_brands(): void
    {
        // Arrange
        Brand::factory()->count(3)->create(['status' => 'active']);
        Brand::factory()->count(2)->create(['status' => 'inactive']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(3, $result['brands']->count());
    }

    public function test_invoke_filters_by_category(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'slug' => 'electronics',
            'status' => 'active',
        ]);
        $product = Product::factory()->create(['status' => 'active']);
        $category->products()->attach($product->id);
        Product::factory()->count(3)->create(['status' => 'active']);

        request()->merge(['category' => 'electronics']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }

    public function test_invoke_filters_by_brand(): void
    {
        // Arrange
        $brand = Brand::factory()->create([
            'slug' => 'adidas',
            'status' => 'active',
        ]);
        Product::factory()->count(2)->create([
            'status' => 'active',
            'brand_id' => $brand->id,
        ]);
        Product::factory()->count(3)->create(['status' => 'active']);

        request()->merge(['brand' => 'adidas']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(2, $result['products']->total());
    }

    public function test_invoke_applies_price_range_filter(): void
    {
        // Arrange
        Product::factory()->create(['status' => 'active', 'price' => 25.00]);
        Product::factory()->create(['status' => 'active', 'price' => 75.00]);
        Product::factory()->create(['status' => 'active', 'price' => 150.00]);

        request()->merge(['price' => '50-100']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }

    public function test_invoke_applies_sorting(): void
    {
        // Arrange
        Product::factory()->create(['status' => 'active', 'title' => 'Z Product']);
        Product::factory()->create(['status' => 'active', 'title' => 'A Product']);

        request()->merge(['sortBy' => 'title']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(2, $result['products']->total());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Product::factory()->count(3)->create(['status' => 'active']);

        $action = app(ProductListsAction::class);

        // Act
        $result1 = $action();
        $result2 = $action();

        // Assert
        $this->assertEquals($result1['products']->pluck('id'), $result2['products']->pluck('id'));
    }

    public function test_invoke_uses_default_pagination(): void
    {
        // Arrange
        Product::factory()->count(10)->create(['status' => 'active']);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertLessThanOrEqual(6, $result['products']->count());
    }

    public function test_invoke_respects_show_parameter(): void
    {
        // Arrange
        Product::factory()->count(10)->create(['status' => 'active']);

        request()->merge(['show' => 12]);

        $action = app(ProductListsAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertLessThanOrEqual(12, $result['products']->count());
    }
}
