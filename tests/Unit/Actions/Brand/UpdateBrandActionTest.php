<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\UpdateBrandAction;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class UpdateBrandActionTest extends ActionTestCase
{
    public function testExecuteUpdatesBrandTitle(): void
    {
        $brand = Brand::factory()->create(['title' => 'Old Title']);

        $dto = new BrandDTO(
            id: $brand->id,
            title: 'New Title',
            slug: 'new-slug',
            status: 'active',
        );

        $action = app(UpdateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('New Title', $result->title);
        $this->assertEquals('new-slug', $result->slug);
    }

    public function testExecuteUpdatesBrandStatus(): void
    {
        $brand = Brand::factory()->create(['status' => 'active']);

        $dto = new BrandDTO(
            id: $brand->id,
            title: $brand->title,
            slug: $brand->slug,
            status: 'inactive',
        );

        $action = app(UpdateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteKeepsExistingSlugWhenNull(): void
    {
        $brand = Brand::factory()->create(['slug' => 'existing-slug']);

        $dto = new BrandDTO(
            id: $brand->id,
            title: 'New Title',
            slug: null,
            status: 'active',
        );

        $action = app(UpdateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('existing-slug', $result->slug);
    }

    public function testExecuteKeepsExistingStatusWhenNull(): void
    {
        $brand = Brand::factory()->create(['status' => 'inactive']);

        $dto = new BrandDTO(
            id: $brand->id,
            title: 'New Title',
            slug: 'new-slug',
            status: null,
        );

        $action = app(UpdateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteUpdatesAllFields(): void
    {
        $brand = Brand::factory()->create();

        $dto = new BrandDTO(
            id: $brand->id,
            title: 'Updated Title',
            slug: 'updated-slug',
            status: 'inactive',
        );

        $action = app(UpdateBrandAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('Updated Title', $result->title);
        $this->assertEquals('updated-slug', $result->slug);
        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteThrowsExceptionForNonExistentBrand(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $dto = new BrandDTO(
            id: 99999,
            title: 'Test',
            slug: 'test',
            status: 'active',
        );

        $action = app(UpdateBrandAction::class);
        $action->execute($dto);
    }
}
