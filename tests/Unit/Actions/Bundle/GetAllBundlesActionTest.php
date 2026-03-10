<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\GetAllBundlesAction;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class GetAllBundlesActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllBundles(): void
    {
        Bundle::factory()->create(['name' => 'Bundle 1']);
        Bundle::factory()->create(['name' => 'Bundle 2']);
        Bundle::factory()->create(['name' => 'Bundle 3']);

        $action = app(GetAllBundlesAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsCollectionOfBundleModels(): void
    {
        Bundle::factory()->count(2)->create();

        $action = app(GetAllBundlesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertInstanceOf(Bundle::class, $result->first());
    }

    public function testExecuteReturnsEmptyCollectionWhenNoBundles(): void
    {
        $action = app(GetAllBundlesAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteIncludesProductsRelationship(): void
    {
        Bundle::factory()->create();

        $action = app(GetAllBundlesAction::class);
        $result = $action->execute();

        $this->assertTrue($result->first()->relationLoaded('products'));
    }

    public function testExecuteIncludesMediaRelationship(): void
    {
        Bundle::factory()->create();

        $action = app(GetAllBundlesAction::class);
        $result = $action->execute();

        $this->assertTrue($result->first()->relationLoaded('media'));
    }
}
