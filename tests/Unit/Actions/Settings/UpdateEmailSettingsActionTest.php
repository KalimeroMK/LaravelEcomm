<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\UpdateEmailSettingsAction;
use Modules\Settings\Models\Setting;
use Tests\Unit\Actions\ActionTestCase;

class UpdateEmailSettingsActionTest extends ActionTestCase
{
    public function test_execute_updates_email_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email_settings' => [
                'mail_driver' => 'smtp',
                'mail_host' => 'old.host.com',
                'mail_port' => '587',
            ],
        ]);

        $action = new UpdateEmailSettingsAction();

        $newData = [
            'mail_host' => 'new.host.com',
            'mail_username' => 'test@example.com',
            'mail_password' => 'secret123',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertArrayHasKey('mail_driver', $result->email_settings);
        $this->assertArrayHasKey('mail_host', $result->email_settings);
        $this->assertArrayHasKey('mail_port', $result->email_settings);
        $this->assertArrayHasKey('mail_username', $result->email_settings);
        $this->assertArrayHasKey('mail_password', $result->email_settings);
        
        // Original values should be preserved
        $this->assertEquals('smtp', $result->email_settings['mail_driver']);
        $this->assertEquals('587', $result->email_settings['mail_port']);
        
        // New values should be merged
        $this->assertEquals('new.host.com', $result->email_settings['mail_host']);
        $this->assertEquals('test@example.com', $result->email_settings['mail_username']);
        $this->assertEquals('secret123', $result->email_settings['mail_password']);
    }

    public function test_execute_creates_email_settings_when_null(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email_settings' => null,
        ]);

        $action = new UpdateEmailSettingsAction();

        $newData = [
            'mail_driver' => 'sendmail',
            'mail_from_address' => 'noreply@shop.com',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('sendmail', $result->email_settings['mail_driver']);
        $this->assertEquals('noreply@shop.com', $result->email_settings['mail_from_address']);
    }

    public function test_execute_merges_nested_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email_settings' => [
                'mail_driver' => 'smtp',
                'mail_host' => 'smtp.mailtrap.io',
                'mail_port' => '2525',
                'mail_encryption' => 'tls',
            ],
        ]);

        $action = new UpdateEmailSettingsAction();

        $newData = [
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '587',
            'mail_username' => 'user@gmail.com',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        
        // Unchanged values should persist
        $this->assertEquals('smtp', $result->email_settings['mail_driver']);
        $this->assertEquals('tls', $result->email_settings['mail_encryption']);
        
        // Updated values should be changed
        $this->assertEquals('smtp.gmail.com', $result->email_settings['mail_host']);
        $this->assertEquals('587', $result->email_settings['mail_port']);
        $this->assertEquals('user@gmail.com', $result->email_settings['mail_username']);
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email_settings' => ['mail_driver' => 'smtp'],
        ]);

        $action = new UpdateEmailSettingsAction();

        // Act
        $result = $action->execute($setting, ['mail_host' => 'test.com']);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        // fresh() should reload from database, ensuring persistence
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_execute_overwrites_existing_keys(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email_settings' => [
                'mail_driver' => 'smtp',
                'mail_host' => 'old.host.com',
            ],
        ]);

        $action = new UpdateEmailSettingsAction();

        // Act - update with same key but new value
        $result = $action->execute($setting, ['mail_host' => 'new.host.com']);

        // Assert
        $this->assertEquals('new.host.com', $result->email_settings['mail_host']);
        $this->assertNotEquals('old.host.com', $result->email_settings['mail_host']);
    }
}
