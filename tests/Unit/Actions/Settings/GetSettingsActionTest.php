<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetSettingsActionTest extends ActionTestCase
{
    public function test_execute_returns_settings_as_array(): void
    {
        // Arrange
        Setting::factory()->create([
            'email' => 'shop@example.com',
            'phone' => '+1234567890',
            'address' => '123 Test Street',
            'site-name' => 'Test Shop',
            'active_template' => 'default',
        ]);

        $repository = new SettingsRepository();
        $action = new GetSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('site-name', $result);
        $this->assertArrayHasKey('active_template', $result);
        $this->assertEquals('shop@example.com', $result['email']);
        $this->assertEquals('+1234567890', $result['phone']);
        $this->assertEquals('123 Test Street', $result['address']);
        $this->assertEquals('Test Shop', $result['site-name']);
        $this->assertEquals('default', $result['active_template']);
    }

    public function test_execute_returns_default_array_when_no_settings_exist(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new GetSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertNull($result['id']);
        $this->assertNull($result['description']);
        $this->assertNull($result['short_des']);
        $this->assertNull($result['logo']);
        $this->assertNull($result['photo']);
        $this->assertNull($result['address']);
        $this->assertNull($result['phone']);
        $this->assertNull($result['email']);
        $this->assertNull($result['site-name']);
        $this->assertEquals('default', $result['active_template']);
        $this->assertNull($result['fb_app_id']);
        $this->assertNull($result['keywords']);
        $this->assertNull($result['google-site-verification']);
        $this->assertNull($result['longitude']);
        $this->assertNull($result['latitude']);
        $this->assertNull($result['google_map_api_key']);
    }

    public function test_execute_returns_first_settings_record(): void
    {
        // Arrange
        Setting::factory()->create([
            'email' => 'first@example.com',
            'phone' => '+1111111111',
        ]);
        Setting::factory()->create([
            'email' => 'second@example.com',
            'phone' => '+2222222222',
        ]);

        $repository = new SettingsRepository();
        $action = new GetSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertIsArray($result);
        // Should return the first created setting
        $this->assertEquals('first@example.com', $result['email']);
        $this->assertEquals('+1111111111', $result['phone']);
    }

    public function test_execute_includes_all_fillable_fields(): void
    {
        // Arrange
        Setting::factory()->create([
            'description' => 'Test description',
            'short_des' => 'Short description',
            'logo' => '/path/to/logo.png',
            'address' => 'Test Address',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'site-name' => 'Test Site',
            'active_template' => 'custom',
            'keywords' => 'test, keywords',
            'google-site-verification' => 'verification_code',
            'longitude' => '12.3456',
            'latitude' => '78.9012',
            'google_map_api_key' => 'api_key_123',
        ]);

        $repository = new SettingsRepository();
        $action = new GetSettingsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('Short description', $result['short_des']);
        $this->assertEquals('/path/to/logo.png', $result['logo']);
        $this->assertEquals('Test Address', $result['address']);
        $this->assertEquals('+1234567890', $result['phone']);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals('Test Site', $result['site-name']);
        $this->assertEquals('custom', $result['active_template']);
        $this->assertEquals('test, keywords', $result['keywords']);
        $this->assertEquals('verification_code', $result['google-site-verification']);
        $this->assertEquals('12.3456', $result['longitude']);
        $this->assertEquals('78.9012', $result['latitude']);
        $this->assertEquals('api_key_123', $result['google_map_api_key']);
    }
}
