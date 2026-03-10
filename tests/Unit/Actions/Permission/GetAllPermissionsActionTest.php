<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Permission;

use Modules\Permission\Actions\GetAllPermissionsAction;
use Modules\Permission\DTOs\PermissionListDTO;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllPermissionsActionTest extends ActionTestCase
{
    public function test_execute_returns_permission_list_dto(): void
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
        $this->assertInstanceOf(PermissionListDTO::class, $result);
        $this->assertCount($initialCount + 3, $result->permissions);
    }

    public function test_execute_returns_dto_with_collection(): void
    {
        // Arrange - database already has seeded permissions
        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(PermissionListDTO::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->permissions->count());
    }

    public function test_execute_returns_permission_models(): void
    {
        // Arrange - create a permission and verify it exists in the collection
        $permissionName = 'test-permission-'.time();
        Permission::create(['name' => $permissionName, 'guard_name' => 'web']);

        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Permission::class, $result->permissions->first());
        $this->assertTrue($result->permissions->contains('name', $permissionName));
        $foundPermission = $result->permissions->firstWhere('name', $permissionName);
        $this->assertEquals('web', $foundPermission->guard_name);
    }

    public function test_execute_includes_permissions_with_different_guards(): void
    {
        // Arrange - get initial count
        $initialCount = Permission::count();
        Permission::create(['name' => 'web-permission-'.time(), 'guard_name' => 'web']);
        Permission::create(['name' => 'api-permission-'.time(), 'guard_name' => 'api']);

        $repository = new PermissionRepository();
        $action = new GetAllPermissionsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount($initialCount + 2, $result->permissions);
    }
}
