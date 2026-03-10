<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class DeleteBundleActionTest extends ActionTestCase
{
    public function testExecuteDeletesBundle(): void
    {
        $bundle = Bundle::factory()->create();
        $bundleId = $bundle->id;

        $action = app(DeleteBundleAction::class);
        $response = $action->execute($bundleId);

        $this->assertDatabaseMissing('bundles', ['id' => $bundleId]);
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    public function testExecuteDeletesMultipleBundles(): void
    {
        $bundle1 = Bundle::factory()->create();
        $bundle2 = Bundle::factory()->create();

        $action = app(DeleteBundleAction::class);
        $action->execute($bundle1->id);
        $action->execute($bundle2->id);

        $this->assertDatabaseMissing('bundles', ['id' => $bundle1->id]);
        $this->assertDatabaseMissing('bundles', ['id' => $bundle2->id]);
    }

    public function testExecuteDeletesBundleWithHighPrice(): void
    {
        $bundle = Bundle::factory()->create(['price' => 999.99]);
        $bundleId = $bundle->id;

        $action = app(DeleteBundleAction::class);
        $action->execute($bundleId);

        $this->assertDatabaseMissing('bundles', ['id' => $bundleId]);
    }

    public function testExecuteDeletesBundleWithZeroPrice(): void
    {
        $bundle = Bundle::factory()->create(['price' => 0.00]);
        $bundleId = $bundle->id;

        $action = app(DeleteBundleAction::class);
        $action->execute($bundleId);

        $this->assertDatabaseMissing('bundles', ['id' => $bundleId]);
    }
}
