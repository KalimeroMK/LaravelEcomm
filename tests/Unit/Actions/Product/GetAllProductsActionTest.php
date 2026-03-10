<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetAllProductsActionTest extends ActionTestCase
{
    public function testExecuteReturnsProductListDTO(): void
    {
        Product::factory()->count(5)->create();

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Modules\Product\DTOs\ProductListDTO::class, $result);
        $this->assertIsArray($result->products);
        $this->assertCount(5, $result->products);
    }

    public function testExecuteReturnsEmptyArrayWhenNoProducts(): void
    {
        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertIsArray($result->products);
        $this->assertCount(0, $result->products);
        $this->assertEmpty($result->products);
    }

    public function testExecuteContainsProductData(): void
    {
        Product::factory()->create(['title' => 'Test Product']);

        $action = app(GetAllProductsAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result->products);
        // Products are converted to arrays in ProductListDTO
        $this->assertEquals('Test Product', $result->products[0]['title']);
    }
}
