<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Modules\Shipping\Actions\DeleteShippingAction;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteShippingActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_shipping_method(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Standard Shipping',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $repository = new ShippingRepository();
        $action = new DeleteShippingAction($repository);

        // Act
        $action->execute($shipping->id);

        // Assert
        $this->assertDatabaseMissing('shipping', [
            'id' => $shipping->id,
            'type' => 'Standard Shipping',
        ]);
    }

    public function test_execute_deletes_shipping_and_verifies_count(): void
    {
        // Arrange
        $shipping1 = Shipping::factory()->create();
        $shipping2 = Shipping::factory()->create();
        $shipping3 = Shipping::factory()->create();

        $repository = new ShippingRepository();
        $action = new DeleteShippingAction($repository);

        // Act
        $action->execute($shipping2->id);

        // Assert
        $this->assertDatabaseHas('shipping', ['id' => $shipping1->id]);
        $this->assertDatabaseMissing('shipping', ['id' => $shipping2->id]);
        $this->assertDatabaseHas('shipping', ['id' => $shipping3->id]);
        $this->assertEquals(2, Shipping::count());
    }

    public function test_execute_deletes_shipping_with_zone_methods(): void
    {
        // Arrange
        $shipping = Shipping::factory()->create([
            'type' => 'Zone Shipping',
        ]);

        // Create zone methods that reference this shipping
        \Modules\Shipping\Models\ShippingZoneMethod::factory()->count(2)->create([
            'shipping_id' => $shipping->id,
        ]);

        $repository = new ShippingRepository();
        $action = new DeleteShippingAction($repository);

        // Act
        $action->execute($shipping->id);

        // Assert
        $this->assertDatabaseMissing('shipping', ['id' => $shipping->id]);
        // Zone methods should be cascade deleted
    }
}
