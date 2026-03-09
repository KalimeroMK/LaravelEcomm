<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetAllProductsActionTest extends ActionTestCase
{
    public function testExecuteReturnsCollectionOfProducts(): void
    {
        Product::factory()->count(5)->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertCount(5, $result);
        $this->assertInstanceOf(Product::class, $result->first());
    }

    public function testExecuteReturnsEmptyCollectionWhenNoProducts(): void
    {
        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteEagerLoadsRelations(): void
    {
        Product::factory()->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $product = $result->first();
        $this->assertTrue($product->relationLoaded('brand'));
        $this->assertTrue($product->relationLoaded('categories'));
    }
}
