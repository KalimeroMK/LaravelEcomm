<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class CreateProductActionTest extends ActionTestCase
{
    public function testExecuteCreatesProductWithValidData(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        
        $dto = new ProductDTO(
            id: null,
            title: 'Test Product',
            slug: 'test-product',
            summary: 'Test summary',
            description: 'Test description',
            stock: 100,
            price: 99.99,
            discount: 0,
            brand_id: $brand->id,
            categories: [$category->id],
            tags: [],
            status: 'active',
            is_featured: false,
            d_deal: 0,
            sku: 'TEST-001',
            special_price: null,
            special_price_start: null,
            special_price_end: null,
        );

        $action = app(StoreProductAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Test Product', $result->title);
        $this->assertEquals('test-product', $result->slug);
        $this->assertDatabaseHas('products', ['title' => 'Test Product']);
    }

    public function testExecuteGeneratesSlugWhenNotProvided(): void
    {
        $brand = Brand::factory()->create();
        
        $dto = new ProductDTO(
            id: null,
            title: 'Another Test Product',
            slug: null,
            summary: 'Test summary',
            description: 'Test description',
            stock: 50,
            price: 49.99,
            discount: 0,
            brand_id: $brand->id,
            categories: [],
            tags: [],
            status: 'active',
            is_featured: false,
            d_deal: 0,
            sku: 'TEST-002',
            special_price: null,
            special_price_start: null,
            special_price_end: null,
        );

        $action = app(StoreProductAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->slug);
        $this->assertStringContainsString('another-test-product', $result->slug);
    }

    public function testExecuteAttachesCategories(): void
    {
        $brand = Brand::factory()->create();
        $categories = Category::factory()->count(2)->create();
        
        $dto = new ProductDTO(
            id: null,
            title: 'Categorized Product',
            slug: 'categorized-product',
            summary: 'Test summary',
            description: 'Test description',
            stock: 10,
            price: 29.99,
            discount: 0,
            brand_id: $brand->id,
            categories: $categories->pluck('id')->toArray(),
            tags: [],
            status: 'active',
            is_featured: false,
            d_deal: 0,
            sku: 'TEST-003',
            special_price: null,
            special_price_start: null,
            special_price_end: null,
        );

        $action = app(StoreProductAction::class);
        $result = $action->execute($dto);

        $this->assertCount(2, $result->categories);
    }
}
