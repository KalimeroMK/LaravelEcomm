<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Illuminate\Http\UploadedFile;
use Modules\Settings\Actions\UpdateSettingsAction;
use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateSettingsActionTest extends ActionTestCase
{
    public function test_execute_updates_setting_with_valid_data(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'old@example.com',
            'phone' => '+1111111111',
            'address' => 'Old Address',
            'site-name' => 'Old Site Name',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'email' => 'new@example.com',
            'phone' => '+9999999999',
            'address' => 'New Address',
            'site-name' => 'New Site Name',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('new@example.com', $result->email);
        $this->assertEquals('+9999999999', $result->phone);
        $this->assertEquals('New Address', $result->address);
        $this->assertEquals('New Site Name', $result['site-name']);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'email' => 'new@example.com',
            'phone' => '+9999999999',
        ]);
    }

    public function test_execute_filters_non_fillable_fields(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'test@example.com',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        // Try to update with non-fillable field
        $data = [
            'email' => 'new@example.com',
            'non_fillable_field' => 'should_be_ignored',
            'another_invalid' => 'also_ignored',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('new@example.com', $result->email);
        // Non-fillable fields should be ignored without error
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_execute_updates_description_and_short_des(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'description' => 'Old description',
            'short_des' => 'Old short',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'description' => 'New description that is longer',
            'short_des' => 'New short description',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('New description that is longer', $result->description);
        $this->assertEquals('New short description', $result->short_des);
    }

    public function test_execute_updates_template(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'active_template' => 'default',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'active_template' => 'custom_theme',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('custom_theme', $result->active_template);
    }

    public function test_execute_updates_keywords_and_verification(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'keywords' => 'old, keywords',
            'google-site-verification' => 'old_code',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'keywords' => 'new, keywords, here',
            'google-site-verification' => 'new_verification_code',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('new, keywords, here', $result->keywords);
        $this->assertEquals('new_verification_code', $result['google-site-verification']);
    }

    public function test_execute_updates_map_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'longitude' => '0.0000',
            'latitude' => '0.0000',
            'google_map_api_key' => 'old_key',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'longitude' => '12.3456',
            'latitude' => '78.9012',
            'google_map_api_key' => 'new_api_key_123',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('12.3456', $result->longitude);
        $this->assertEquals('78.9012', $result->latitude);
        $this->assertEquals('new_api_key_123', $result->google_map_api_key);
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'old@example.com',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        // Act
        $result = $action->execute($setting->id, ['email' => 'new@example.com']);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('new@example.com', $result->email);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_execute_handles_empty_data_array(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        // Act - update with empty array
        $result = $action->execute($setting->id, []);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        // Values should remain unchanged
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('+1234567890', $result->phone);
    }

    public function test_execute_updates_logo_field(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'logo' => '/old/logo.png',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'logo' => '/new/logo.png',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('/new/logo.png', $result->logo);
    }

    public function test_execute_updates_site_name(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'site-name' => 'Old Site Name',
        ]);

        $repository = new SettingsRepository();
        $action = new UpdateSettingsAction($repository);

        $data = [
            'site-name' => 'New Site Name',
        ];

        // Act
        $result = $action->execute($setting->id, $data);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('New Site Name', $result['site-name']);
    }
}
