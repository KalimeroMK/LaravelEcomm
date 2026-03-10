<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Permission;

use Modules\Permission\Actions\DeletePermissionAction;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeletePermissionActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_permission(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'permission-to-delete',
            'guard_name' => 'web',
        ]);

        $repository = new PermissionRepository();
        $action = new DeletePermissionAction($repository);

        // Act
        $action->execute($permission->id);

        // Assert
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
            'name' => 'permission-to-delete',
        ]);
    }

    public function test_execute_deletes_permission_and_preserves_other_permissions(): void
    {
        // Arrange
        $initialCount = Permission::count();
        $permission1 = Permission::create(['name' => 'permission-1', 'guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'permission-2', 'guard_name' => 'web']);
        $permission3 = Permission::create(['name' => 'permission-3', 'guard_name' => 'web']);

        $repository = new PermissionRepository();
        $action = new DeletePermissionAction($repository);

        // Act
        $action->execute($permission2->id);

        // Assert
        $this->assertDatabaseHas('permissions', ['id' => $permission1->id]);
        $this->assertDatabaseMissing('permissions', ['id' => $permission2->id]);
        $this->assertDatabaseHas('permissions', ['id' => $permission3->id]);
        $this->assertEquals($initialCount + 2, Permission::count());
    }

    public function test_execute_deletes_permission_with_roles(): void
    {
        // Arrange
        $permission = Permission::create(['name' => 'permission-with-roles', 'guard_name' => 'web']);
        $role = \Modules\Role\Models\Role::create(['name' => 'test-role-'.time(), 'guard_name' => 'web']);
        $permission->roles()->attach($role);

        $repository = new PermissionRepository();
        $action = new DeletePermissionAction($repository);

        // Act
        $action->execute($permission->id);

        // Assert
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
        // Role should still exist, just detached
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }
}
