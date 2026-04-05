<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Front\Actions\BundleDetailAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class BundleDetailActionTest extends ActionTestCase
{
    public function test_invoke_returns_bundle_detail(): void
    {
        // Arrange
        $bundle = Bundle::factory()->create(['name' => 'Summer Bundle']);
        $product = Product::factory()->create(['status' => 'active']);
        $bundle->products()->attach($product->id);

        $repository = app(BundleRepository::class);
        $action = new BundleDetailAction($repository);

        // Act
        $result = $action($bundle->slug);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('bundle', $result);
        $this->assertArrayHasKey('related', $result);
        $this->assertEquals($bundle->id, $result['bundle']->id);
    }

    public function test_invoke_aborts_for_nonexistent_bundle(): void
    {
        // Arrange
        $repository = app(BundleRepository::class);
        $action = new BundleDetailAction($repository);

        // Assert & Act
        try {
            $action('nonexistent-bundle');
            $this->fail('Expected abort to be called');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $this->assertEquals('Bundle not found', $e->getMessage());
        }
    }

    public function test_invoke_loads_products_relationship(): void
    {
        // Arrange
        $bundle = Bundle::factory()->create(['name' => 'Test Bundle']);
        $product1 = Product::factory()->create(['status' => 'active']);
        $product2 = Product::factory()->create(['status' => 'active']);
        $bundle->products()->attach([$product1->id, $product2->id]);

        $repository = app(BundleRepository::class);
        $action = new BundleDetailAction($repository);

        // Act
        $result = $action($bundle->slug);

        // Assert
        $this->assertTrue($result['bundle']->relationLoaded('products'));
        $this->assertCount(2, $result['bundle']->products);
    }

    public function test_invoke_returns_related_products(): void
    {
        // Arrange
        $bundle = Bundle::factory()->create(['name' => 'Test Bundle']);
        $bundleProduct = Product::factory()->create(['status' => 'active']);
        $bundle->products()->attach($bundleProduct->id);

        // Create additional products not in bundle
        Product::factory()->count(5)->create(['status' => 'active']);

        $repository = app(BundleRepository::class);
        $action = new BundleDetailAction($repository);

        // Act
        $result = $action($bundle->slug);

        // Assert
        $this->assertLessThanOrEqual(8, $result['related']->count());
        $this->assertFalse($result['related']->pluck('id')->contains($bundleProduct->id));
    }

    public function test_invoke_only_returns_active_related_products(): void
    {
        // Arrange
        $bundle = Bundle::factory()->create(['name' => 'Active Bundle']);
        $bundleProduct = Product::factory()->create(['status' => 'active']);
        $bundle->products()->attach($bundleProduct->id);

        Product::factory()->count(3)->create(['status' => 'active']);
        Product::factory()->count(2)->create(['status' => 'inactive']);

        $repository = app(BundleRepository::class);
        $action = new BundleDetailAction($repository);

        // Act
        $result = $action($bundle->slug);

        // Assert
        foreach ($result['related'] as $product) {
            $this->assertEquals('active', $product->status);
        }
    }
}
