<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Shipping\Actions\UpdateShippingAction;
use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateShippingActionTest extends ActionTestCase
{
    public function test_execute_updates_shipping_with_dto(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Old Shipping',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: $shipping->id,
            type: 'Updated Shipping',
            price: 15.00,
            status: 'inactive',
        );

        // Act
        $result = $action->execute($shipping->id, $dto);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('Updated Shipping', $result->type);
        $this->assertEquals(15.00, $result->price);
        $this->assertEquals('inactive', $result->status);
        $this->assertDatabaseHas('shipping', [
            'id' => $shipping->id,
            'type' => 'Updated Shipping',
            'price' => 15.00,
            'status' => 'inactive',
        ]);
    }

    public function test_execute_updates_only_type(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: $shipping->id,
            type: 'Express',
            price: 10.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($shipping->id, $dto);

        // Assert
        $this->assertEquals('Express', $result->type);
        $this->assertEquals(10.00, $result->price);
        $this->assertEquals('active', $result->status);
    }

    public function test_execute_updates_only_price(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: $shipping->id,
            type: 'Standard',
            price: 20.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($shipping->id, $dto);

        // Assert
        $this->assertEquals('Standard', $result->type);
        $this->assertEquals(20.00, $result->price);
    }

    public function test_execute_updates_only_status(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: $shipping->id,
            type: 'Standard',
            price: 10.00,
            status: 'inactive',
        );

        // Act
        $result = $action->execute($shipping->id, $dto);

        // Assert
        $this->assertEquals('inactive', $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: 999999,
            type: 'Nonexistent',
            price: 10.00,
            status: 'active',
        );

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999, $dto);
    }

    public function test_execute_updates_to_free_shipping(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new UpdateShippingAction($repository);

        $dto = new ShippingDTO(
            id: $shipping->id,
            type: 'Free Shipping',
            price: 0.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($shipping->id, $dto);

        // Assert
        $this->assertEquals('Free Shipping', $result->type);
        $this->assertEquals(0.00, $result->price);
    }
}
