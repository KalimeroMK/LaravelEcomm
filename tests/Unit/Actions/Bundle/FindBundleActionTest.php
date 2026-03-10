<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\FindBundleAction;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class FindBundleActionTest extends ActionTestCase
{
    public function testExecuteFindsBundleById(): void
    {
        $bundle = Bundle::factory()->create(['name' => 'Test Bundle']);

        $action = app(FindBundleAction::class);
        $result = $action->execute($bundle->id);

        $this->assertInstanceOf(Bundle::class, $result);
        $this->assertEquals($bundle->id, $result->id);
        $this->assertEquals('Test Bundle', $result->name);
    }

    public function testExecuteFindsBundleWithDescription(): void
    {
        $bundle = Bundle::factory()->create([
            'name' => 'Test Bundle',
            'description' => 'Detailed Description',
        ]);

        $action = app(FindBundleAction::class);
        $result = $action->execute($bundle->id);

        $this->assertEquals('Detailed Description', $result->description);
    }

    public function testExecuteFindsBundleWithPrice(): void
    {
        $bundle = Bundle::factory()->create(['price' => 149.99]);

        $action = app(FindBundleAction::class);
        $result = $action->execute($bundle->id);

        $this->assertEquals(149.99, $result->price);
    }

    public function testExecuteThrowsExceptionForNonExistentBundle(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $action = app(FindBundleAction::class);
        $action->execute(99999);
    }

    public function testExecuteReturnsModelInstance(): void
    {
        $bundle = Bundle::factory()->create();

        $action = app(FindBundleAction::class);
        $result = $action->execute($bundle->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $result);
    }
}
