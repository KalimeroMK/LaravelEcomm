<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Illuminate\Support\Collection;
use Modules\Product\Actions\GetProductComparisonAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetProductComparisonActionTest extends ActionTestCase
{
    private array $storage = [];

    private function createAction(): GetProductComparisonAction
    {
        return new GetProductComparisonAction(
            getStorage: fn () => $this->storage
        );
    }

    public function testExecuteReturnsProductsInComparison(): void
    {
        $products = Product::factory()->count(3)->create();
        $this->storage = $products->pluck('id')->toArray();

        $action = $this->createAction();
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoProducts(): void
    {
        $this->storage = [];
        $action = $this->createAction();
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteLoadsRequiredRelations(): void
    {
        $products = Product::factory()->count(2)->create();
        $this->storage = $products->pluck('id')->toArray();

        $action = $this->createAction();
        $result = $action->execute();

        $this->assertCount(2, $result);
        $this->assertTrue($result->first()->relationLoaded('media'));
        $this->assertTrue($result->first()->relationLoaded('attributeValues'));
        $this->assertTrue($result->first()->relationLoaded('brand'));
        $this->assertTrue($result->first()->relationLoaded('categories'));
    }

    public function testExecuteReturnsOnlyExistingProducts(): void
    {
        $product = Product::factory()->create();
        $this->storage = [$product->id, 99999, 88888]; // Include non-existent IDs

        $action = $this->createAction();
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertEquals($product->id, $result->first()->id);
    }

    public function testExecuteReturnsCorrectProductData(): void
    {
        $product = Product::factory()->create([
            'title' => 'Comparison Test Product',
            'price' => 149.99,
        ]);
        $this->storage = [$product->id];

        $action = $this->createAction();
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertEquals('Comparison Test Product', $result->first()->title);
        $this->assertEquals(149.99, $result->first()->price);
    }
}
