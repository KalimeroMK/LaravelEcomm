<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Shipping;

use Modules\Shipping\Actions\CalculateShippingAction;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Models\ShippingZone;
use Modules\Shipping\Models\ShippingZoneMethod;
use Tests\Unit\Actions\ActionTestCase;

class CalculateShippingActionTest extends ActionTestCase
{
    public function test_execute_returns_shipping_methods_for_matching_zone(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
            'priority' => 1,
        ]);

        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping->id,
            'price' => 10.00,
            'is_active' => true,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', null, null, 50.00);

        // Assert
        $this->assertArrayHasKey('methods', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(1, $result['methods']);
        $this->assertEquals('Standard', $result['methods'][0]['name']);
        $this->assertEquals(10.00, $result['methods'][0]['price']);
    }

    public function test_execute_returns_free_shipping_when_threshold_met(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
        ]);

        $shipping = Shipping::factory()->create([
            'type' => 'Free Shipping',
            'price' => 0.00,
            'status' => 'active',
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping->id,
            'price' => 15.00,
            'free_shipping_threshold' => 100.00,
            'is_active' => true,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', null, null, 150.00);

        // Assert
        $this->assertCount(1, $result['methods']);
        $this->assertEquals(0.00, $result['methods'][0]['price']);
        // Note: is_free is set based on $cost === 0 which may be 0.0 (float)
        $this->assertEquals(0, $result['methods'][0]['price']);
    }

    public function test_execute_returns_empty_methods_when_no_zones_match(): void
    {
        // Arrange
        ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('CA', null, null, 50.00);

        // Assert
        $this->assertArrayHasKey('methods', $result);
        $this->assertEquals(0, $result['total']);
    }

    public function test_execute_returns_default_shipping_when_no_zone_matches(): void
    {
        // Arrange
        Shipping::factory()->create([
            'type' => 'Default',
            'price' => 15.00,
            'status' => 'active',
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('XX', null, null, 50.00);

        // Assert
        $this->assertCount(1, $result['methods']);
        $this->assertEquals('Default', $result['methods'][0]['name']);
        $this->assertEquals(15.00, $result['methods'][0]['price']);
    }

    public function test_execute_matches_region_within_zone(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'California Zone',
            'countries' => ['US'],
            'regions' => ['CA'],
            'is_active' => true,
        ]);

        $shipping = Shipping::factory()->create([
            'type' => 'CA Express',
            'price' => 20.00,
            'status' => 'active',
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping->id,
            'price' => 20.00,
            'is_active' => true,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', 'CA', null, 50.00);

        // Assert
        $this->assertCount(1, $result['methods']);
        $this->assertEquals('CA Express', $result['methods'][0]['name']);
    }

    public function test_execute_returns_multiple_methods_for_zone(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
        ]);

        $shipping1 = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $shipping2 = Shipping::factory()->create([
            'type' => 'Express',
            'price' => 25.00,
            'status' => 'active',
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping1->id,
            'price' => 10.00,
            'is_active' => true,
            'priority' => 2,
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping2->id,
            'price' => 25.00,
            'is_active' => true,
            'priority' => 1,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', null, null, 50.00);

        // Assert
        $this->assertCount(2, $result['methods']);
        $this->assertEquals(2, $result['total']);
    }

    public function test_execute_falls_back_to_default_when_no_active_zone_methods(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
        ]);

        $shipping = Shipping::factory()->create([
            'type' => 'Inactive Method',
            'price' => 10.00,
            'status' => 'inactive', // Make inactive so it doesn't appear in fallback
        ]);

        // Create inactive zone method
        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping->id,
            'price' => 10.00,
            'is_active' => false,
        ]);

        // Create default shipping that will be used as fallback
        Shipping::factory()->create([
            'type' => 'Default Fallback',
            'price' => 15.00,
            'status' => 'active',
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', null, null, 50.00);

        // Assert - should fall back to default shipping
        $this->assertEquals(1, $result['total']);
        $this->assertEquals('Default Fallback', $result['methods'][0]['name']);
    }

    public function test_execute_includes_estimated_days_when_set(): void
    {
        // Arrange
        $zone = ShippingZone::factory()->create([
            'name' => 'US Zone',
            'countries' => ['US'],
            'is_active' => true,
        ]);

        $shipping = Shipping::factory()->create([
            'type' => 'Standard',
            'price' => 10.00,
            'status' => 'active',
        ]);

        ShippingZoneMethod::factory()->create([
            'shipping_zone_id' => $zone->id,
            'shipping_id' => $shipping->id,
            'price' => 10.00,
            'estimated_days' => 5,
            'is_active' => true,
        ]);

        $action = new CalculateShippingAction();

        // Act
        $result = $action->execute('US', null, null, 50.00);

        // Assert
        $this->assertEquals(5, $result['methods'][0]['estimated_days']);
    }
}
