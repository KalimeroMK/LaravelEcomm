<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\FindBrandAction;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class FindBrandActionTest extends ActionTestCase
{
    public function testExecuteFindsBrandById(): void
    {
        $brand = Brand::factory()->create(['title' => 'Test Brand']);

        $action = app(FindBrandAction::class);
        $result = $action->execute($brand->id);

        $this->assertInstanceOf(Brand::class, $result);
        $this->assertEquals($brand->id, $result->id);
        $this->assertEquals('Test Brand', $result->title);
    }

    public function testExecuteFindsActiveBrand(): void
    {
        $brand = Brand::factory()->create(['status' => 'active']);

        $action = app(FindBrandAction::class);
        $result = $action->execute($brand->id);

        $this->assertEquals('active', $result->status);
    }

    public function testExecuteFindsInactiveBrand(): void
    {
        $brand = Brand::factory()->create(['status' => 'inactive']);

        $action = app(FindBrandAction::class);
        $result = $action->execute($brand->id);

        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteThrowsExceptionForNonExistentBrand(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $action = app(FindBrandAction::class);
        $action->execute(99999);
    }
}
