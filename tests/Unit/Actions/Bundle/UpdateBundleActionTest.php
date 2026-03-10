<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class UpdateBundleActionTest extends ActionTestCase
{
    public function testExecuteUpdatesBundleName(): void
    {
        $bundle = Bundle::factory()->create(['name' => 'Old Name']);

        $dto = new BundleDTO(
            id: $bundle->id,
            name: 'New Name',
            description: $bundle->description,
            price: $bundle->price,
        );

        $action = app(UpdateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('New Name', $result->name);
    }

    public function testExecuteUpdatesBundleDescription(): void
    {
        $bundle = Bundle::factory()->create(['description' => 'Old Description']);

        $dto = new BundleDTO(
            id: $bundle->id,
            name: $bundle->name,
            description: 'New Description',
            price: $bundle->price,
        );

        $action = app(UpdateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('New Description', $result->description);
    }

    public function testExecuteUpdatesBundlePrice(): void
    {
        $bundle = Bundle::factory()->create(['price' => 50.00]);

        $dto = new BundleDTO(
            id: $bundle->id,
            name: $bundle->name,
            description: $bundle->description,
            price: 100.00,
        );

        $action = app(UpdateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(100.00, $result->price);
    }

    public function testExecuteUpdatesAllFields(): void
    {
        $bundle = Bundle::factory()->create();

        $dto = new BundleDTO(
            id: $bundle->id,
            name: 'Updated Name',
            description: 'Updated Description',
            price: 199.99,
        );

        $action = app(UpdateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('Updated Description', $result->description);
        $this->assertEquals(199.99, $result->price);
    }

    public function testExecuteReturnsModelInstance(): void
    {
        $bundle = Bundle::factory()->create();

        $dto = new BundleDTO(
            id: $bundle->id,
            name: 'Updated Name',
            description: $bundle->description,
            price: $bundle->price,
        );

        $action = app(UpdateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $result);
    }

    public function testExecuteThrowsExceptionForNonExistentBundle(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $dto = new BundleDTO(
            id: 99999,
            name: 'Test Name',
            description: 'Test Description',
            price: 99.99,
        );

        $action = app(UpdateBundleAction::class);
        $action->execute($dto);
    }

    public function testExecutePersistsChangesToDatabase(): void
    {
        $bundle = Bundle::factory()->create(['name' => 'Original Name']);

        $dto = new BundleDTO(
            id: $bundle->id,
            name: 'Database Updated Name',
            description: $bundle->description,
            price: $bundle->price,
        );

        $action = app(UpdateBundleAction::class);
        $action->execute($dto);

        $this->assertDatabaseHas('bundles', [
            'id' => $bundle->id,
            'name' => 'Database Updated Name',
        ]);
    }
}
