<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Category\Models\Category;
use Modules\Front\Actions\ProductDetailAction;
use Modules\Product\Models\Product;
use Modules\Product\Services\RecentlyViewedService;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ProductDetailActionTest extends ActionTestCase
{
    public function test_invoke_returns_product_detail(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'slug' => 'test-product',
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id);

        $action = app(ProductDetailAction::class);

        // Act
        $result = $action('test-product');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('product_detail', $result);
        $this->assertArrayHasKey('related', $result);
        $this->assertEquals($product->id, $result['product_detail']->id);
    }

    public function test_invoke_returns_related_products(): void
    {
        // Arrange
        $category = Category::factory()->create(['title' => 'Electronics']);
        $product1 = Product::factory()->create([
            'slug' => 'product-one',
            'status' => 'active',
        ]);
        $product1->categories()->attach($category->id);

        $product2 = Product::factory()->create(['status' => 'active']);
        $product2->categories()->attach($category->id);

        $action = app(ProductDetailAction::class);

        // Act
        $result = $action('product-one');

        // Assert
        $this->assertLessThanOrEqual(8, $result['related']->count());
    }

    public function test_invoke_excludes_current_product_from_related(): void
    {
        // Arrange
        $category = Category::factory()->create(['title' => 'Gadgets']);
        $product1 = Product::factory()->create([
            'slug' => 'main-product',
            'status' => 'active',
        ]);
        $product1->categories()->attach($category->id);

        $product2 = Product::factory()->create(['status' => 'active']);
        $product2->categories()->attach($category->id);

        $action = app(ProductDetailAction::class);

        // Act
        $result = $action('main-product');

        // Assert
        $this->assertFalse($result['related']->pluck('id')->contains($product1->id));
    }

    public function test_invoke_only_returns_active_related_products(): void
    {
        // Arrange
        $category = Category::factory()->create(['title' => 'Tech']);
        $product = Product::factory()->create([
            'slug' => 'active-product',
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id);

        // Active related product
        $related1 = Product::factory()->create(['status' => 'active']);
        $related1->categories()->attach($category->id);

        // Inactive related product
        $related2 = Product::factory()->create(['status' => 'inactive']);
        $related2->categories()->attach($category->id);

        $action = app(ProductDetailAction::class);

        // Act
        $result = $action('active-product');

        // Assert
        foreach ($result['related'] as $related) {
            $this->assertEquals('active', $related->status);
        }
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Product::factory()->create([
            'slug' => 'cached-product',
            'status' => 'active',
        ]);

        $action = app(ProductDetailAction::class);

        // Act
        $result1 = $action('cached-product');
        $result2 = $action('cached-product');

        // Assert
        $this->assertEquals($result1['product_detail']->id, $result2['product_detail']->id);
    }

    public function test_invoke_tracks_recently_viewed_for_authenticated_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'slug' => 'trackable-product',
            'status' => 'active',
        ]);

        $service = $this->createMock(RecentlyViewedService::class);
        $service->expects($this->once())
            ->method('addProduct')
            ->with($product->id, $user->id);

        $action = new ProductDetailAction($service);

        // Act
        $action('trackable-product');
    }
}
