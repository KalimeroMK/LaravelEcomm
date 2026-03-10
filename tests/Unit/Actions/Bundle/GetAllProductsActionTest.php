<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\GetAllProductsAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetAllProductsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllProducts(): void
    {
        Product::factory()->count(3)->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsCollectionOfProductModels(): void
    {
        Product::factory()->count(2)->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertInstanceOf(Product::class, $result->first());
    }

    public function testExecuteReturnsEmptyCollectionWhenNoProducts(): void
    {
        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsProductsWithRelations(): void
    {
        Product::factory()->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $product = $result->first();
        $this->assertTrue($product->relationLoaded('brand'));
        $this->assertTrue($product->relationLoaded('categories'));
    }

    public function testExecuteReturnsCorrectProductData(): void
    {
        Product::factory()->create([
            'title' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $product = $result->first();
        $this->assertEquals('Test Product', $product->title);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals(10, $product->stock);
    }
}
