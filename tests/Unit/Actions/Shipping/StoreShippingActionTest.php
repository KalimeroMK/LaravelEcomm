<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Modules\Shipping\Actions\StoreShippingAction;
use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;
use Tests\Unit\Actions\ActionTestCase;

class StoreShippingActionTest extends ActionTestCase
{
    public function test_execute_creates_shipping_with_dto(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new StoreShippingAction($repository);

        $dto = new ShippingDTO(
            id: null,
            type: 'Standard Delivery',
            price: 10.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('Standard Delivery', $result->type);
        $this->assertEquals(10.00, $result->price);
        $this->assertEquals('active', $result->status);
        $this->assertDatabaseHas('shipping', [
            'type' => 'Standard Delivery',
            'price' => 10.00,
            'status' => 'active',
        ]);
    }

    public function test_execute_creates_free_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new StoreShippingAction($repository);

        $dto = new ShippingDTO(
            id: null,
            type: 'Free Shipping',
            price: 0.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('Free Shipping', $result->type);
        $this->assertEquals(0.00, $result->price);
    }

    public function test_execute_creates_inactive_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new StoreShippingAction($repository);

        $dto = new ShippingDTO(
            id: null,
            type: 'Inactive Shipping',
            price: 15.00,
            status: 'inactive',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('inactive', $result->status);
    }

    public function test_execute_creates_express_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new StoreShippingAction($repository);

        $dto = new ShippingDTO(
            id: null,
            type: 'Express Delivery',
            price: 25.00,
            status: 'active',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('Express Delivery', $result->type);
        $this->assertEquals(25.00, $result->price);
    }

    public function test_execute_creates_multiple_shipping_methods(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new StoreShippingAction($repository);

        $dto1 = new ShippingDTO(id: null, type: 'Standard', price: 10.00, status: 'active');
        $dto2 = new ShippingDTO(id: null, type: 'Express', price: 20.00, status: 'active');

        // Act
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        // Assert
        $this->assertEquals(2, Shipping::count());
        $this->assertDatabaseHas('shipping', ['type' => 'Standard', 'price' => 10.00]);
        $this->assertDatabaseHas('shipping', ['type' => 'Express', 'price' => 20.00]);
    }
}
