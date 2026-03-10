<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Banner\Models\Banner;
use Modules\Front\Actions\IndexAction;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class IndexActionTest extends ActionTestCase
{
    public function test_invoke_returns_homepage_data(): void
    {
        // Arrange
        $product1 = Product::factory()->create([
            'status' => 'active',
            'is_featured' => true,
            'price' => 100.00,
        ]);
        $product2 = Product::factory()->create([
            'status' => 'active',
            'is_featured' => false,
            'price' => 50.00,
        ]);
        $post = Post::factory()->create(['status' => 'active']);
        $banner = Banner::factory()->create(['status' => 'active']);

        $action = app(IndexAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('featured_products', $result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('banners', $result);
        $this->assertArrayHasKey('latest_products', $result);
        $this->assertArrayHasKey('hot_products', $result);
    }

    public function test_invoke_returns_featured_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create([
            'status' => 'active',
            'is_featured' => true,
            'price' => 100.00,
        ]);

        $action = app(IndexAction::class);

        // Act
        $result = $action();

        // Assert - should return up to 4 featured products
        $this->assertLessThanOrEqual(4, $result['featured_products']->count());
        $this->assertGreaterThan(0, $result['featured_products']->count());
    }

    public function test_invoke_returns_latest_products(): void
    {
        // Arrange
        Product::factory()->count(5)->create(['status' => 'active']);

        $action = app(IndexAction::class);

        // Act
        $result = $action();

        // Assert - should return up to 4 latest products
        $this->assertLessThanOrEqual(4, $result['latest_products']->count());
        $this->assertGreaterThan(0, $result['latest_products']->count());
    }

    public function test_invoke_returns_posts(): void
    {
        // Arrange
        Post::factory()->count(5)->create(['status' => 'active']);

        $action = app(IndexAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertLessThanOrEqual(3, $result['posts']->count());
        $this->assertGreaterThan(0, $result['posts']->count());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Product::factory()->create([
            'status' => 'active',
            'is_featured' => true,
        ]);

        $action = app(IndexAction::class);

        // Act - First call should cache
        $result1 = $action();
        $result2 = $action();

        // Assert
        $this->assertEquals($result1['featured_products']->pluck('id'), $result2['featured_products']->pluck('id'));
    }

    public function test_invoke_returns_only_active_featured_products(): void
    {
        // Arrange
        $activeProduct = Product::factory()->create([
            'status' => 'active',
            'is_featured' => true,
        ]);
        $inactiveProduct = Product::factory()->create([
            'status' => 'inactive',
            'is_featured' => true,
        ]);

        $action = app(IndexAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertTrue($result['featured_products']->pluck('id')->contains($activeProduct->id));
        $this->assertFalse($result['featured_products']->pluck('id')->contains($inactiveProduct->id));
    }
}
