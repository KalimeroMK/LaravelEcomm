<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindSettingActionTest extends ActionTestCase
{
    public function test_execute_returns_setting_by_id(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $repository = new SettingsRepository();
        $action = new FindSettingAction($repository);

        // Act
        $result = $action->execute($setting->id);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($setting->id, $result->id);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('+1234567890', $result->phone);
    }

    public function test_execute_returns_first_setting_when_no_id_provided(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'email' => 'first@example.com',
        ]);
        Setting::factory()->create([
            'email' => 'second@example.com',
        ]);

        $repository = new SettingsRepository();
        $action = new FindSettingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($setting->id, $result->id);
        $this->assertEquals('first@example.com', $result->email);
    }

    public function test_execute_throws_exception_when_id_not_found(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new FindSettingAction($repository);

        // Assert & Act - findOrFail throws ModelNotFoundException
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(9999);
    }

    public function test_execute_returns_null_when_no_settings_exist(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new FindSettingAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertNull($result);
    }

    public function test_execute_returns_null_when_id_is_zero(): void
    {
        // Arrange
        $repository = new SettingsRepository();
        $action = new FindSettingAction($repository);

        // Act - passing 0 should not find by ID and return first(), which is null
        $result = $action->execute(0);

        // Assert
        $this->assertNull($result);
    }
}
