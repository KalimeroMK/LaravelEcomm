<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Illuminate\Support\Collection;
use Modules\Shipping\Actions\GetAllShippingAction;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllShippingActionTest extends ActionTestCase
{
    public function test_execute_returns_all_shipping_methods(): void
    {
        // Arrange
        Shipping::factory()->create(['type' => 'Standard', 'status' => 'active']);
        Shipping::factory()->create(['type' => 'Express', 'status' => 'active']);
        Shipping::factory()->create(['type' => 'Free', 'status' => 'inactive']);

        $repository = new ShippingRepository();
        $action = new GetAllShippingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_execute_returns_collection_of_shipping_models(): void
    {
        // Arrange
        Shipping::factory()->count(2)->create();

        $repository = new ShippingRepository();
        $action = new GetAllShippingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Shipping::class, $result->first());
    }

    public function test_execute_returns_empty_collection_when_no_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new GetAllShippingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_execute_returns_shipping_ordered_by_id_desc(): void
    {
        // Arrange
        $shipping1 = Shipping::factory()->create(['type' => 'First Shipping']);
        $shipping2 = Shipping::factory()->create(['type' => 'Second Shipping']);
        $shipping3 = Shipping::factory()->create(['type' => 'Third Shipping']);

        $repository = new ShippingRepository();
        $action = new GetAllShippingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertEquals($shipping3->id, $result->first()->id);
        $this->assertEquals($shipping1->id, $result->last()->id);
    }

    public function test_execute_includes_all_shipping_attributes(): void
    {
        // Arrange
        Shipping::factory()->create([
            'type' => 'Test Shipping',
            'price' => 15.50,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new GetAllShippingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('Test Shipping', $result->first()->type);
        $this->assertEquals(15.50, $result->first()->price);
        $this->assertEquals('active', $result->first()->status);
    }
}
