<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Shipping\Actions\FindShippingAction;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindShippingActionTest extends ActionTestCase
{
    public function test_execute_finds_shipping_by_id(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Findable Shipping',
            'price' => 15.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new FindShippingAction($repository);

        // Act
        $result = $action->execute($shipping->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals($shipping->id, $result->id);
        $this->assertEquals('Findable Shipping', $result->type);
        $this->assertEquals(15.00, $result->price);
    }

    public function test_execute_finds_shipping_with_all_attributes(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Complete Shipping',
            'price' => 25.00,
            'status' => 'inactive',
        ]);

        $repository = new ShippingRepository();
        $action = new FindShippingAction($repository);

        // Act
        $result = $action->execute($shipping->id);

        // Assert
        $this->assertEquals('Complete Shipping', $result->type);
        $this->assertEquals(25.00, $result->price);
        $this->assertEquals('inactive', $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_shipping(): void
    {
        // Arrange
        $repository = new ShippingRepository();
        $action = new FindShippingAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }

    public function test_execute_finds_shipping_with_orders(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Shipping with Orders',
        ]);

        // Create orders using this shipping
        \Modules\Order\Models\Order::factory()->count(2)->create([
            'shipping_id' => $shipping->id,
        ]);

        $repository = new ShippingRepository();
        $action = new FindShippingAction($repository);

        // Act
        $result = $action->execute($shipping->id);

        // Assert
        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals($shipping->id, $result->id);
        $this->assertEquals('Shipping with Orders', $result->type);
    }
}
