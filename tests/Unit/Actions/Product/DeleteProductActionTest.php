<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class DeleteProductActionTest extends ActionTestCase
{
    public function testExecuteDeletesProductSuccessfully(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $action = app(DeleteProductAction::class);
        $action->execute($product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testExecuteDoesNotThrowForNonExistentProduct(): void
    {
        $action = app(DeleteProductAction::class);
        
        // Should not throw an exception
        $action->execute(99999);
        
        $this->assertTrue(true); // Test passes if we reach this point
    }
}
