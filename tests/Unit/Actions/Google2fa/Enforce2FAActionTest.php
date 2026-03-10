<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Enforce2FAAction;
use Modules\Google2fa\Models\Google2faSetting;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\Unit\Actions\ActionTestCase;

class Enforce2FAActionTest extends ActionTestCase
{
    private Enforce2FAAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
        $this->action = new Enforce2FAAction();
    }

    public function test_execute_returns_true_when_enforce_for_admins_is_true_and_user_is_admin(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update(['enforce_for_admins' => true, 'enforce_for_users' => false]);

        $user = User::factory()->create();
        $user->assignRole('admin');

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_true_when_enforce_for_admins_is_true_and_user_is_super_admin(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update(['enforce_for_admins' => true, 'enforce_for_users' => false]);

        $user = User::factory()->create();
        $user->assignRole('super-admin');

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_true_when_enforce_for_users_is_true(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update(['enforce_for_admins' => false, 'enforce_for_users' => true]);

        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_true_when_user_has_enforced_role(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update([
            'enforce_for_admins' => false,
            'enforce_for_users' => false,
            'enforced_roles' => ['manager'],
        ]);

        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $user->assignRole('manager');

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_no_enforcement_rules_match(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update([
            'enforce_for_admins' => false,
            'enforce_for_users' => false,
            'enforced_roles' => [],
        ]);

        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_returns_false_for_admin_when_enforce_for_admins_is_false(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update(['enforce_for_admins' => false, 'enforce_for_users' => false]);

        $user = User::factory()->create();
        $user->assignRole('admin');

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_checks_multiple_roles(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update([
            'enforce_for_admins' => false,
            'enforce_for_users' => false,
            'enforced_roles' => ['editor', 'manager', 'owner'],
        ]);

        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $user->assignRole('manager');

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertTrue($result);
    }
}
