<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\DeleteProductMediaAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class DeleteProductMediaActionTest extends ActionTestCase
{
    public function testExecuteDeletesProductMedia(): void
    {
        $product = Product::factory()->create();
        $action = app(DeleteProductMediaAction::class);

        // Add a media file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_media');
        file_put_contents($tempFile, 'test content');
        $media = $product->addMedia($tempFile)->toMediaCollection('product');
        // Note: addMedia moves the file, so we don't need to unlink it

        $this->assertDatabaseHas('media', ['id' => $media->id]);

        $action->execute($product->id, $media->id);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function testExecuteThrowsExceptionForNonExistentProduct(): void
    {
        $action = app(DeleteProductMediaAction::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product not found with ID: 99999');

        $action->execute(99999, 1);
    }

    public function testExecuteDoesNothingForNonExistentMedia(): void
    {
        $product = Product::factory()->create();
        $action = app(DeleteProductMediaAction::class);

        // Should not throw any exception
        $action->execute($product->id, 99999);

        $this->assertTrue(true); // Test passes if we reach this point
    }
}
