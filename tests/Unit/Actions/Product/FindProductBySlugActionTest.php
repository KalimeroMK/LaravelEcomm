<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\FindProductBySlugAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class FindProductBySlugActionTest extends ActionTestCase
{
    public function testExecuteFindsProductBySlug(): void
    {
        $product = Product::factory()->create([
            'slug' => 'test-product-slug',
        ]);

        $action = app(FindProductBySlugAction::class);
        $result = $action->execute('test-product-slug');

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals('test-product-slug', $result->slug);
    }

    public function testExecuteReturnsNullForNonExistentSlug(): void
    {
        $action = app(FindProductBySlugAction::class);
        $result = $action->execute('non-existent-slug');

        $this->assertNull($result);
    }

    public function testExecuteFindsProductWithCorrectAttributes(): void
    {
        $product = Product::factory()->create([
            'slug' => 'another-product',
            'title' => 'Test Product Title',
            'price' => 99.99,
        ]);

        $action = app(FindProductBySlugAction::class);
        $result = $action->execute('another-product');

        $this->assertNotNull($result);
        $this->assertEquals('Test Product Title', $result->title);
        $this->assertEquals(99.99, $result->price);
    }
}
