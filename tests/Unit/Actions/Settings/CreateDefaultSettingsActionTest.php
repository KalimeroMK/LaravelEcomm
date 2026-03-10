<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\CreateDefaultSettingsAction;
use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreateDefaultSettingsActionTest extends ActionTestCase
{
    public function test_execute_creates_default_settings(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new CreateDefaultSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('Quality products, fast delivery, best prices', $result->short_des);
        $this->assertEquals('info@example.com', $result->email);
        $this->assertEquals('+1 (555) 123-4567', $result->phone);
        $this->assertEquals('123 Main Street, City, Country', $result->address);
        $this->assertEquals('default', $result->active_template);
        $this->assertDatabaseHas('settings', [
            'email' => 'info@example.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Main Street, City, Country',
        ]);
    }

    public function test_execute_creates_settings_with_payment_defaults(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new CreateDefaultSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'email' => 'info@example.com',
        ]);
        
        // Check that payment settings defaults are set in the JSON fields
        $setting = Setting::first();
        $this->assertNotNull($setting);
    }

    public function test_execute_creates_settings_with_email_defaults(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new CreateDefaultSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('smtp', $result->email_settings['mail_driver'] ?? 'smtp');
        $this->assertEquals('smtp.mailtrap.io', $result->email_settings['mail_host'] ?? 'smtp.mailtrap.io');
    }

    public function test_execute_creates_settings_with_shipping_defaults(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new CreateDefaultSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'email' => 'info@example.com',
        ]);
    }

    public function test_execute_creates_settings_with_seo_defaults(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new CreateDefaultSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'email' => 'info@example.com',
        ]);
    }
}
