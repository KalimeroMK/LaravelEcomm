<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class UpdateProductActionTest extends ActionTestCase
{
    public function testExecuteUpdatesProductSuccessfully(): void
    {
        $product = Product::factory()->create([
            'title' => 'Old Title',
            'price' => 50.00,
        ]);

        $dto = new ProductDTO(
            title: 'New Title',
            slug: $product->slug,
            summary: 'Updated summary',
            description: 'Updated description',
            stock: 200,
            price: 75.00,
            discount: 10,
            brand_id: $product->brand_id,
            categories: [],
            tags: [],
            status: 'active',
            is_featured: true,
            sku: $product->sku,
            meta_title: 'Updated Meta',
            meta_description: 'Updated meta description',
        );

        $action = app(UpdateProductAction::class);
        $result = $action->execute($product->id, $dto);

        $this->assertEquals('New Title', $result->title);
        $this->assertEquals(75.00, $result->price);
        $this->assertEquals(10, $result->discount);
        $this->assertTrue($result->is_featured);
    }

    public function testExecuteThrowsExceptionForNonExistentProduct(): void
    {
        $dto = new ProductDTO(
            title: 'Test',
            slug: 'test',
            summary: 'Test',
            description: 'Test',
            stock: 10,
            price: 10.00,
            discount: 0,
            brand_id: 1,
            categories: [],
            tags: [],
            status: 'active',
            is_featured: false,
            sku: 'TEST-999',
            meta_title: null,
            meta_description: null,
        );

        $action = app(UpdateProductAction::class);
        
        $this->expectException(\InvalidArgumentException::class);
        $action->execute(99999, $dto);
    }

    public function testExecuteUpdatesProductCategories(): void
    {
        $product = Product::factory()->create();
        $newCategories = \Modules\Category\Models\Category::factory()->count(3)->create();

        $dto = new ProductDTO(
            title: $product->title,
            slug: $product->slug,
            summary: $product->summary,
            description: $product->description,
            stock: $product->stock,
            price: $product->price,
            discount: $product->discount,
            brand_id: $product->brand_id,
            categories: $newCategories->pluck('id')->toArray(),
            tags: [],
            status: 'active',
            is_featured: false,
            sku: $product->sku,
            meta_title: null,
            meta_description: null,
        );

        $action = app(UpdateProductAction::class);
        $result = $action->execute($product->id, $dto);

        $this->assertCount(3, $result->fresh()->categories);
    }
}
