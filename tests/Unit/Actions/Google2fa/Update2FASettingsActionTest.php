<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Update2FASettingsAction;
use Modules\Google2fa\Models\Google2faSetting;
use Tests\Unit\Actions\ActionTestCase;

class Update2FASettingsActionTest extends ActionTestCase
{
    private Update2FASettingsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Update2FASettingsAction();
    }

    public function test_execute_updates_settings(): void
    {
        // Arrange
        Google2faSetting::getSettings(); // Ensure settings exist
        
        $data = [
            'enforce_for_admins' => true,
            'enforce_for_users' => false,
            'enforced_roles' => ['admin', 'manager'],
            'recovery_codes_count' => 12,
            'require_backup_codes' => false,
        ];

        // Act
        $result = $this->action->execute($data);

        // Assert
        $this->assertInstanceOf(Google2faSetting::class, $result);
        $this->assertTrue($result->enforce_for_admins);
        $this->assertFalse($result->enforce_for_users);
        $this->assertEquals(['admin', 'manager'], $result->enforced_roles);
        $this->assertEquals(12, $result->recovery_codes_count);
        $this->assertFalse($result->require_backup_codes);
    }

    public function test_execute_persists_changes_to_database(): void
    {
        // Arrange
        Google2faSetting::getSettings();
        
        $data = [
            'enforce_for_admins' => true,
            'recovery_codes_count' => 8,
        ];

        // Act
        $this->action->execute($data);

        // Assert
        $this->assertDatabaseHas('google2fa_settings', [
            'enforce_for_admins' => true,
            'recovery_codes_count' => 8,
        ]);
    }

    public function test_execute_uses_default_values_for_missing_fields(): void
    {
        // Arrange
        Google2faSetting::getSettings();
        
        $data = [
            'enforce_for_admins' => true,
        ];

        // Act
        $result = $this->action->execute($data);

        // Assert
        $this->assertTrue($result->enforce_for_admins);
        $this->assertFalse($result->enforce_for_users);
        $this->assertIsArray($result->enforced_roles);
        $this->assertEmpty($result->enforced_roles);
        $this->assertEquals(10, $result->recovery_codes_count);
        $this->assertFalse($result->require_backup_codes);
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $initialSettings = Google2faSetting::getSettings();
        
        $data = [
            'enforce_for_admins' => true,
        ];

        // Act
        $result = $this->action->execute($data);

        // Assert
        $this->assertEquals($initialSettings->id, $result->id);
        $this->assertTrue($result->enforce_for_admins);
    }

    public function test_execute_handles_empty_enforced_roles(): void
    {
        // Arrange
        Google2faSetting::getSettings();
        
        $data = [
            'enforce_for_admins' => true,
            'enforced_roles' => [],
        ];

        // Act
        $result = $this->action->execute($data);

        // Assert
        $this->assertIsArray($result->enforced_roles);
        $this->assertEmpty($result->enforced_roles);
    }

    public function test_execute_handles_null_values_in_data(): void
    {
        // Arrange
        Google2faSetting::getSettings();
        
        $data = [
            'enforce_for_admins' => null,
            'enforce_for_users' => null,
        ];

        // Act
        $result = $this->action->execute($data);

        // Assert
        $this->assertFalse($result->enforce_for_admins);
        $this->assertFalse($result->enforce_for_users);
    }
}
