<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\UpdateShippingSettingsAction;
use Modules\Settings\Models\Setting;
use Tests\Unit\Actions\ActionTestCase;

class UpdateShippingSettingsActionTest extends ActionTestCase
{
    public function test_execute_updates_shipping_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => [
                'default_shipping_method' => 'standard',
                'free_shipping_threshold' => 50,
                'flat_rate_shipping' => 10,
            ],
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'free_shipping_threshold' => 100,
            'flat_rate_shipping' => 15,
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertArrayHasKey('default_shipping_method', $result->shipping_settings);
        $this->assertArrayHasKey('free_shipping_threshold', $result->shipping_settings);
        $this->assertArrayHasKey('flat_rate_shipping', $result->shipping_settings);
        
        // Unchanged value should persist
        $this->assertEquals('standard', $result->shipping_settings['default_shipping_method']);
        
        // New values should be merged
        $this->assertEquals(100, $result->shipping_settings['free_shipping_threshold']);
        $this->assertEquals(15, $result->shipping_settings['flat_rate_shipping']);
    }

    public function test_execute_creates_shipping_settings_when_null(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => null,
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'default_shipping_method' => 'express',
            'free_shipping_threshold' => 75.50,
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('express', $result->shipping_settings['default_shipping_method']);
        $this->assertEquals(75.50, $result->shipping_settings['free_shipping_threshold']);
    }

    public function test_execute_updates_shipping_costs(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => [
                'standard_shipping_cost' => 5.00,
                'express_shipping_cost' => 15.00,
                'overnight_shipping_cost' => 25.00,
            ],
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'standard_shipping_cost' => 7.99,
            'express_shipping_cost' => 19.99,
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals(7.99, $result->shipping_settings['standard_shipping_cost']);
        $this->assertEquals(19.99, $result->shipping_settings['express_shipping_cost']);
        $this->assertEquals(25.00, $result->shipping_settings['overnight_shipping_cost']); // Unchanged
    }

    public function test_execute_changes_default_shipping_method(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => [
                'default_shipping_method' => 'standard',
                'standard_shipping_cost' => 5.00,
                'express_shipping_cost' => 15.00,
            ],
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'default_shipping_method' => 'express',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('express', $result->shipping_settings['default_shipping_method']);
        $this->assertEquals(5.00, $result->shipping_settings['standard_shipping_cost']); // Unchanged
        $this->assertEquals(15.00, $result->shipping_settings['express_shipping_cost']); // Unchanged
    }

    public function test_execute_updates_free_shipping_threshold(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => [
                'free_shipping_threshold' => 50,
                'free_shipping_enabled' => true,
            ],
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'free_shipping_threshold' => 0, // Free shipping for all orders
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals(0, $result->shipping_settings['free_shipping_threshold']);
        $this->assertTrue($result->shipping_settings['free_shipping_enabled']); // Unchanged
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => ['default_shipping_method' => 'standard'],
        ]);

        $action = new UpdateShippingSettingsAction();

        // Act
        $result = $action->execute($setting, ['flat_rate_shipping' => 10]);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_execute_preserves_all_existing_shipping_settings(): void
    {
        // Arrange
        $originalSettings = [
            'default_shipping_method' => 'standard',
            'free_shipping_threshold' => 100,
            'flat_rate_shipping' => 10,
            'standard_shipping_cost' => 5.00,
            'express_shipping_cost' => 15.00,
            'overnight_shipping_cost' => 25.00,
            'free_shipping_enabled' => true,
            'international_shipping_enabled' => false,
        ];
        
        $setting = Setting::factory()->create([
            'shipping_settings' => $originalSettings,
        ]);

        $action = new UpdateShippingSettingsAction();

        // Act - update only one field
        $result = $action->execute($setting, ['flat_rate_shipping' => 12.99]);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('standard', $result->shipping_settings['default_shipping_method']);
        $this->assertEquals(100, $result->shipping_settings['free_shipping_threshold']);
        $this->assertEquals(12.99, $result->shipping_settings['flat_rate_shipping']); // Updated
        $this->assertEquals(5.00, $result->shipping_settings['standard_shipping_cost']);
        $this->assertEquals(15.00, $result->shipping_settings['express_shipping_cost']);
        $this->assertEquals(25.00, $result->shipping_settings['overnight_shipping_cost']);
        $this->assertTrue($result->shipping_settings['free_shipping_enabled']);
        $this->assertFalse($result->shipping_settings['international_shipping_enabled']);
    }

    public function test_execute_adds_new_shipping_options(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'shipping_settings' => [
                'default_shipping_method' => 'standard',
                'standard_shipping_cost' => 5.00,
            ],
        ]);

        $action = new UpdateShippingSettingsAction();

        $newData = [
            'pickup_in_store_enabled' => true,
            'pickup_in_store_cost' => 0,
            'local_delivery_enabled' => true,
            'local_delivery_cost' => 8.00,
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('standard', $result->shipping_settings['default_shipping_method']); // Unchanged
        $this->assertEquals(5.00, $result->shipping_settings['standard_shipping_cost']); // Unchanged
        $this->assertTrue($result->shipping_settings['pickup_in_store_enabled']);
        $this->assertEquals(0, $result->shipping_settings['pickup_in_store_cost']);
        $this->assertTrue($result->shipping_settings['local_delivery_enabled']);
        $this->assertEquals(8.00, $result->shipping_settings['local_delivery_cost']);
    }
}
