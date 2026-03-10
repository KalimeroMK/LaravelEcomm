<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Bundle\Models\Bundle;
use Modules\Front\Actions\ProductBundlesAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class ProductBundlesActionTest extends ActionTestCase
{
    public function test_invoke_returns_bundles_data(): void
    {
        // Arrange
        Bundle::factory()->count(5)->create();
        Product::factory()->count(3)->create(['status' => 'active']);

        $action = app(ProductBundlesAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('recent_products', $result);
        $this->assertArrayHasKey('products', $result);
    }

    public function test_invoke_returns_paginated_bundles(): void
    {
        // Arrange
        Bundle::factory()->count(10)->create();
        Product::factory()->create(['status' => 'active']);

        $action = app(ProductBundlesAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(6, $result['products']->items()); // default per page
    }

    public function test_invoke_returns_recent_products(): void
    {
        // Arrange
        Bundle::factory()->create();
        Product::factory()->count(5)->create(['status' => 'active']);

        $action = app(ProductBundlesAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(3, $result['recent_products']);
    }

    public function test_invoke_only_returns_active_recent_products(): void
    {
        // Arrange
        Bundle::factory()->create();
        Product::factory()->count(3)->create(['status' => 'active']);
        Product::factory()->count(2)->create(['status' => 'inactive']);

        $action = app(ProductBundlesAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(3, $result['recent_products']);
    }

    public function test_invoke_returns_bundle_products(): void
    {
        // Arrange
        $bundle = Bundle::factory()->create();
        $product = Product::factory()->create(['status' => 'active']);
        $bundle->products()->attach($product->id);

        $action = app(ProductBundlesAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertGreaterThanOrEqual(1, $result['products']->total());
    }
}
