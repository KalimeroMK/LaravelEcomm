<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class CreateBundleActionTest extends ActionTestCase
{
    public function testExecuteCreatesBundle(): void
    {
        $dto = new BundleDTO(
            id: null,
            name: 'Test Bundle',
            description: 'Test Description',
            price: 99.99,
        );

        $action = app(CreateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Bundle::class, $result);
        $this->assertEquals('Test Bundle', $result->name);
        $this->assertEquals('Test Description', $result->description);
        $this->assertEquals(99.99, $result->price);
    }

    public function testExecuteCreatesBundleWithZeroPrice(): void
    {
        $dto = new BundleDTO(
            id: null,
            name: 'Free Bundle',
            description: 'Free Description',
            price: 0.00,
        );

        $action = app(CreateBundleAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(0.00, $result->price);
    }

    public function testExecuteCreatesMultipleBundles(): void
    {
        $dto1 = new BundleDTO(id: null, name: 'Bundle 1', description: 'Desc 1', price: 10.00);
        $dto2 = new BundleDTO(id: null, name: 'Bundle 2', description: 'Desc 2', price: 20.00);
        $dto3 = new BundleDTO(id: null, name: 'Bundle 3', description: 'Desc 3', price: 30.00);

        $action = app(CreateBundleAction::class);
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);
        $result3 = $action->execute($dto3);

        $this->assertNotEquals($result1->id, $result2->id);
        $this->assertNotEquals($result2->id, $result3->id);

        $this->assertDatabaseHas('bundles', ['name' => 'Bundle 1']);
        $this->assertDatabaseHas('bundles', ['name' => 'Bundle 2']);
        $this->assertDatabaseHas('bundles', ['name' => 'Bundle 3']);
    }

    public function testExecuteSavesBundleToDatabase(): void
    {
        $dto = new BundleDTO(
            id: null,
            name: 'Database Bundle',
            description: 'Database Description',
            price: 49.99,
        );

        $action = app(CreateBundleAction::class);
        $action->execute($dto);

        $this->assertDatabaseHas('bundles', [
            'name' => 'Database Bundle',
            'description' => 'Database Description',
            'price' => 49.99,
        ]);
    }
}
