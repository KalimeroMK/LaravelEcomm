<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Get2FASettingsAction;
use Modules\Google2fa\Models\Google2faSetting;
use Tests\Unit\Actions\ActionTestCase;

class Get2FASettingsActionTest extends ActionTestCase
{
    private Get2FASettingsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Get2FASettingsAction();
    }

    public function test_execute_returns_settings_instance(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertInstanceOf(Google2faSetting::class, $result);
    }

    public function test_execute_creates_default_settings_if_not_exists(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertDatabaseHas('google2fa_settings', [
            'id' => $result->id,
        ]);
    }

    public function test_execute_returns_existing_settings(): void
    {
        // Arrange
        $existingSettings = Google2faSetting::getSettings();
        $existingSettings->update([
            'enforce_for_admins' => true,
            'recovery_codes_count' => 15,
        ]);

        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertEquals($existingSettings->id, $result->id);
        $this->assertTrue($result->enforce_for_admins);
        $this->assertEquals(15, $result->recovery_codes_count);
    }

    public function test_execute_returns_settings_with_default_values(): void
    {
        // Act
        $result = $this->action->execute();

        // Assert
        $this->assertFalse($result->enforce_for_admins);
        $this->assertFalse($result->enforce_for_users);
        $this->assertIsArray($result->enforced_roles);
        $this->assertEmpty($result->enforced_roles);
        $this->assertEquals(10, $result->recovery_codes_count);
        $this->assertTrue($result->require_backup_codes);
    }

    public function test_execute_returns_same_instance_on_multiple_calls(): void
    {
        // Act
        $result1 = $this->action->execute();
        $result2 = $this->action->execute();

        // Assert
        $this->assertEquals($result1->id, $result2->id);
    }
}
