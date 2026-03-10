<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Illuminate\Support\Collection;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Modules\Role\Actions\GetAllPermissionsAction;
use Tests\Unit\Actions\ActionTestCase;

class GetAllPermissionsActionTest extends ActionTestCase
{
    public function test_execute_returns_collection_of_permissions(): void
    {
        // Arrange - get initial count
        $initialCount = Permission::count();
        Permission::create(['name' => 'permission-1-'.time(), 'guard_name' => 'web']);
        Permission::create(['name' => 'permission-2-'.time(), 'guard_name' => 'web']);
        Permission::create(['name' => 'permission-3-'.time(), 'guard_name' => 'web']);

        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($initialCount + 3, $result);
    }

    public function test_execute_returns_collection(): void
    {
        // Arrange - database already has seeded permissions
        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count());
    }

    public function test_execute_returns_permission_models(): void
    {
        // Arrange - create a permission and verify it exists in the collection
        $permissionName = 'test-permission-'.time();
        $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'web']);

        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Permission::class, $result->first());
        $this->assertTrue($result->contains('name', $permissionName));
        $foundPermission = $result->firstWhere('name', $permissionName);
        $this->assertEquals('web', $foundPermission->guard_name);
    }

    public function test_execute_includes_permissions_with_different_guards(): void
    {
        // Arrange - get initial count
        $initialCount = Permission::count();
        Permission::create(['name' => 'web-perm-'.time(), 'guard_name' => 'web']);
        Permission::create(['name' => 'api-perm-'.time(), 'guard_name' => 'api']);

        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount($initialCount + 2, $result);
    }
}
