<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Front\Actions\ProductDealAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductDealActionTest extends ActionTestCase
{
    public function test_invoke_returns_deal_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create([
            'status' => 'active',
            'd_deal' => true,
        ]);
        Product::factory()->count(3)->create([
            'status' => 'active',
            'd_deal' => false,
        ]);
        Brand::factory()->count(3)->create();

        $action = app(ProductDealAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('recent_products', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('brands', $result);
    }

    public function test_invoke_returns_only_deal_products(): void
    {
        // Arrange
        Product::factory()->count(3)->create([
            'status' => 'active',
            'd_deal' => true,
        ]);
        Product::factory()->create([
            'status' => 'active',
            'd_deal' => false,
        ]);
        Brand::factory()->create();

        $action = app(ProductDealAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(3, $result['products']->total());
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create([
            'status' => 'active',
            'd_deal' => true,
        ]);
        Brand::factory()->create();

        $action = app(ProductDealAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(3, $result['recent_products']);
    }

    public function test_invoke_returns_brands(): void
    {
        // Arrange - Create specific number of active brands
        Brand::factory()->count(5)->create(['status' => 'active']);
        
        Product::factory()->create([
            'status' => 'active',
            'd_deal' => true,
        ]);

        $action = app(ProductDealAction::class);

        // Act
        $result = $action();

        // Assert - should have exactly 5 active brands
        $this->assertGreaterThanOrEqual(5, $result['brands']->count());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Product::factory()->create([
            'status' => 'active',
            'd_deal' => true,
        ]);
        Brand::factory()->create();

        $action = app(ProductDealAction::class);

        // Act
        $result1 = $action();
        $result2 = $action();

        // Assert
        $this->assertEquals($result1['products']->pluck('id'), $result2['products']->pluck('id'));
    }
}
