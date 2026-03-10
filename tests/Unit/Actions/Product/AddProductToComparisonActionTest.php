<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\AddProductToComparisonAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class AddProductToComparisonActionTest extends ActionTestCase
{
    private array $storage = [];

    private function createAction(): AddProductToComparisonAction
    {
        return new AddProductToComparisonAction(
            getStorage: fn () => $this->storage,
            putStorage: fn (array $value) => $this->storage = $value
        );
    }

    public function testExecuteAddsProductToComparison(): void
    {
        $product = Product::factory()->create();
        $action = $this->createAction();

        $result = $action->execute($product->id);

        $this->assertEquals($product->id, $result['product_id']);
        $this->assertEquals(1, $result['comparison_count']);
        $this->assertContains($product->id, $result['products']);
    }

    public function testExecuteDoesNotAddDuplicateProduct(): void
    {
        $product = Product::factory()->create();
        $action = $this->createAction();

        // Add product first time
        $action->execute($product->id);
        // Add same product again
        $result = $action->execute($product->id);

        $this->assertEquals(1, $result['comparison_count']);
        $this->assertCount(1, $result['products']);
    }

    public function testExecuteLimitsToLastFourProducts(): void
    {
        $products = Product::factory()->count(5)->create();
        $action = $this->createAction();

        foreach ($products as $product) {
            $action->execute($product->id);
        }

        // Should only keep last 4 products
        $this->assertCount(4, $this->storage);
        // First product should be removed
        $this->assertNotContains($products[0]->id, $this->storage);
        // Last 4 products should be present
        $this->assertContains($products[1]->id, $this->storage);
        $this->assertContains($products[2]->id, $this->storage);
        $this->assertContains($products[3]->id, $this->storage);
        $this->assertContains($products[4]->id, $this->storage);
    }

    public function testExecuteReturnsCorrectComparisonCount(): void
    {
        $products = Product::factory()->count(3)->create();
        $action = $this->createAction();

        foreach ($products as $index => $product) {
            $result = $action->execute($product->id);
            $this->assertEquals($index + 1, $result['comparison_count']);
        }
    }

    public function testExecuteThrowsExceptionForNonExistentProduct(): void
    {
        $action = $this->createAction();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(99999);
    }
}
