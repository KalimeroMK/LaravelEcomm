<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\GetAllBrandsAction;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class GetAllBrandsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllBrands(): void
    {
        // Create brands
        Brand::factory()->create(['status' => 'active']);
        Brand::factory()->create(['status' => 'active']);
        Brand::factory()->create(['status' => 'inactive']);

        $action = app(GetAllBrandsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsCollectionOfBrandModels(): void
    {
        Brand::factory()->count(2)->create();

        $action = app(GetAllBrandsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertInstanceOf(Brand::class, $result->first());
    }

    public function testExecuteReturnsEmptyCollectionWhenNoBrands(): void
    {
        $action = app(GetAllBrandsAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
