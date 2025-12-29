<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Product\Actions\GetFeaturedProductsAction;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;
use Tests\TestCase;

class GetFeaturedProductsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_only_featured_products(): void
    {
        // Arrange
        Product::factory()->create(['is_featured' => true, 'title' => 'Featured Product 1']);
        Product::factory()->create(['is_featured' => true, 'title' => 'Featured Product 2']);
        Product::factory()->create(['is_featured' => false, 'title' => 'Regular Product']);

        $repository = new ProductRepository();
        $action = new GetFeaturedProductsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn ($product) => $product->is_featured === true));
    }

    public function test_execute_returns_empty_collection_when_no_featured_products(): void
    {
        // Arrange
        Product::factory()->create(['is_featured' => false]);
        Product::factory()->create(['is_featured' => false]);

        $repository = new ProductRepository();
        $action = new GetFeaturedProductsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
