<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\DeleteBrandAction;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class DeleteBrandActionTest extends ActionTestCase
{
    public function testExecuteDeletesBrand(): void
    {
        $brand = Brand::factory()->create();
        $brandId = $brand->id;

        $action = app(DeleteBrandAction::class);
        $action->execute($brandId);

        $this->assertDatabaseMissing('brands', ['id' => $brandId]);
    }

    public function testExecuteDeletesMultipleBrands(): void
    {
        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();

        $action = app(DeleteBrandAction::class);
        $action->execute($brand1->id);
        $action->execute($brand2->id);

        $this->assertDatabaseMissing('brands', ['id' => $brand1->id]);
        $this->assertDatabaseMissing('brands', ['id' => $brand2->id]);
    }

    public function testExecuteDeletesActiveBrand(): void
    {
        $brand = Brand::factory()->create(['status' => 'active']);
        $brandId = $brand->id;

        $action = app(DeleteBrandAction::class);
        $action->execute($brandId);

        $this->assertDatabaseMissing('brands', ['id' => $brandId]);
    }

    public function testExecuteDeletesInactiveBrand(): void
    {
        $brand = Brand::factory()->create(['status' => 'inactive']);
        $brandId = $brand->id;

        $action = app(DeleteBrandAction::class);
        $action->execute($brandId);

        $this->assertDatabaseMissing('brands', ['id' => $brandId]);
    }
}
