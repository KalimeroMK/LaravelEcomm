<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Modules\Permission\Models\Permission;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteRoleActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_role(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'role-to-delete',
            'guard_name' => 'web',
        ]);

        $repository = new RoleRepository();
        $action = new DeleteRoleAction($repository);

        // Act
        $action->execute($role->id);

        // Assert
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
            'name' => 'role-to-delete',
        ]);
    }

    public function test_execute_deletes_role_and_preserves_other_roles(): void
    {
        // Arrange
        $initialCount = Role::count();
        $role1 = Role::create(['name' => 'role-1', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'role-2', 'guard_name' => 'web']);
        $role3 = Role::create(['name' => 'role-3', 'guard_name' => 'web']);

        $repository = new RoleRepository();
        $action = new DeleteRoleAction($repository);

        // Act
        $action->execute($role2->id);

        // Assert
        $this->assertDatabaseHas('roles', ['id' => $role1->id]);
        $this->assertDatabaseMissing('roles', ['id' => $role2->id]);
        $this->assertDatabaseHas('roles', ['id' => $role3->id]);
        $this->assertEquals($initialCount + 2, Role::count());
    }

    public function test_execute_deletes_role_with_permissions(): void
    {
        // Arrange
        $role = Role::create(['name' => 'role-with-perms', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'test-permission-'.time(), 'guard_name' => 'web']);
        $role->permissions()->attach($permission);

        $repository = new RoleRepository();
        $action = new DeleteRoleAction($repository);

        // Act
        $action->execute($role->id);

        // Assert
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
        // Permission should still exist, just detached
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }
}
