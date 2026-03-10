<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\UpdatePaymentSettingsAction;
use Modules\Settings\Models\Setting;
use Tests\Unit\Actions\ActionTestCase;

class UpdatePaymentSettingsActionTest extends ActionTestCase
{
    public function test_execute_updates_payment_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => [
                'stripe_enabled' => false,
                'stripe_public_key' => 'pk_old',
                'paypal_enabled' => false,
            ],
        ]);

        $action = new UpdatePaymentSettingsAction();

        $newData = [
            'stripe_enabled' => true,
            'stripe_public_key' => 'pk_new',
            'stripe_secret_key' => 'sk_new',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertArrayHasKey('stripe_enabled', $result->payment_settings);
        $this->assertArrayHasKey('stripe_public_key', $result->payment_settings);
        $this->assertArrayHasKey('stripe_secret_key', $result->payment_settings);
        $this->assertArrayHasKey('paypal_enabled', $result->payment_settings);
        
        // Original unchanged value should be preserved
        $this->assertFalse($result->payment_settings['paypal_enabled']);
        
        // New values should be merged
        $this->assertTrue($result->payment_settings['stripe_enabled']);
        $this->assertEquals('pk_new', $result->payment_settings['stripe_public_key']);
        $this->assertEquals('sk_new', $result->payment_settings['stripe_secret_key']);
    }

    public function test_execute_creates_payment_settings_when_null(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => null,
        ]);

        $action = new UpdatePaymentSettingsAction();

        $newData = [
            'stripe_enabled' => true,
            'cod_enabled' => true,
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertTrue($result->payment_settings['stripe_enabled']);
        $this->assertTrue($result->payment_settings['cod_enabled']);
    }

    public function test_execute_enables_paypal_payment(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => [
                'stripe_enabled' => false,
                'paypal_enabled' => false,
                'paypal_client_id' => null,
                'paypal_client_secret' => null,
                'paypal_mode' => 'sandbox',
            ],
        ]);

        $action = new UpdatePaymentSettingsAction();

        $newData = [
            'paypal_enabled' => true,
            'paypal_client_id' => 'client_id_123',
            'paypal_client_secret' => 'client_secret_456',
            'paypal_mode' => 'live',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertTrue($result->payment_settings['paypal_enabled']);
        $this->assertEquals('client_id_123', $result->payment_settings['paypal_client_id']);
        $this->assertEquals('client_secret_456', $result->payment_settings['paypal_client_secret']);
        $this->assertEquals('live', $result->payment_settings['paypal_mode']);
        // Unchanged value should persist
        $this->assertFalse($result->payment_settings['stripe_enabled']);
    }

    public function test_execute_enables_bank_transfer(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => [
                'bank_transfer_enabled' => false,
            ],
        ]);

        $action = new UpdatePaymentSettingsAction();

        $newData = [
            'bank_transfer_enabled' => true,
            'bank_account_details' => 'Bank: Test Bank, Account: 123456789',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertTrue($result->payment_settings['bank_transfer_enabled']);
        $this->assertEquals('Bank: Test Bank, Account: 123456789', $result->payment_settings['bank_account_details']);
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => ['cod_enabled' => true],
        ]);

        $action = new UpdatePaymentSettingsAction();

        // Act
        $result = $action->execute($setting, ['stripe_enabled' => false]);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_execute_merges_multiple_payment_methods(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'payment_settings' => [
                'stripe_enabled' => false,
                'stripe_public_key' => 'pk_test',
                'paypal_enabled' => false,
                'paypal_client_id' => 'old_client',
                'cod_enabled' => true,
                'bank_transfer_enabled' => false,
            ],
        ]);

        $action = new UpdatePaymentSettingsAction();

        $newData = [
            'stripe_enabled' => true,
            'paypal_enabled' => true,
            'paypal_client_id' => 'new_client',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertTrue($result->payment_settings['stripe_enabled']);
        $this->assertTrue($result->payment_settings['paypal_enabled']);
        $this->assertTrue($result->payment_settings['cod_enabled']); // Unchanged
        $this->assertFalse($result->payment_settings['bank_transfer_enabled']); // Unchanged
        $this->assertEquals('pk_test', $result->payment_settings['stripe_public_key']); // Unchanged
        $this->assertEquals('new_client', $result->payment_settings['paypal_client_id']); // Updated
    }
}
