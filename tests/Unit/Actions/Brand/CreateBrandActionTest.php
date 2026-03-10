<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\CreateBrandAction;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class CreateBrandActionTest extends ActionTestCase
{
    public function testExecuteCreatesBrand(): void
    {
        $dto = new BrandDTO(
            id: null,
            title: 'Nike',
            slug: 'nike',
            status: 'active',
        );

        $action = app(CreateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Brand::class, $result);
        $this->assertEquals('Nike', $result->title);
        $this->assertEquals('nike', $result->slug);
        $this->assertEquals('active', $result->status);
    }

    public function testExecuteCreatesInactiveBrand(): void
    {
        $dto = new BrandDTO(
            id: null,
            title: 'Adidas',
            slug: 'adidas',
            status: 'inactive',
        );

        $action = app(CreateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteCreatesMultipleBrands(): void
    {
        $dto1 = new BrandDTO(id: null, title: 'Nike', slug: 'nike', status: 'active');
        $dto2 = new BrandDTO(id: null, title: 'Adidas', slug: 'adidas', status: 'active');
        $dto3 = new BrandDTO(id: null, title: 'Puma', slug: 'puma', status: 'active');

        $action = app(CreateBrandAction::class);
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);
        $result3 = $action->execute($dto3);

        $this->assertNotEquals($result1->id, $result2->id);
        $this->assertNotEquals($result2->id, $result3->id);

        $this->assertDatabaseHas('brands', ['slug' => 'nike']);
        $this->assertDatabaseHas('brands', ['slug' => 'adidas']);
        $this->assertDatabaseHas('brands', ['slug' => 'puma']);
    }

    public function testExecuteSavesBrandToDatabase(): void
    {
        $dto = new BrandDTO(
            id: null,
            title: 'Test Brand',
            slug: 'test-brand',
            status: 'active',
        );

        $action = app(CreateBrandAction::class);
        $action->execute($dto);

        $this->assertDatabaseHas('brands', [
            'title' => 'Test Brand',
            'slug' => 'test-brand',
            'status' => 'active',
        ]);
    }
}
