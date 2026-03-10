<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Permission;

use Modules\Permission\Actions\CreatePermissionAction;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreatePermissionActionTest extends ActionTestCase
{
    public function test_execute_creates_permission_with_dto(): void
    {
        // Arrange
        $repository = new PermissionRepository();
        $action = new CreatePermissionAction($repository);

        $dto = new PermissionDTO(
            id: null,
            name: 'new-permission-'.time(),
            guard_name: 'web',
            created_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertStringStartsWith('new-permission-', $result->name);
        $this->assertEquals('web', $result->guard_name);
        $this->assertDatabaseHas('permissions', [
            'name' => $result->name,
            'guard_name' => 'web',
        ]);
    }

    public function test_execute_creates_permission_with_api_guard(): void
    {
        // Arrange
        $repository = new PermissionRepository();
        $action = new CreatePermissionAction($repository);

        $dto = new PermissionDTO(
            id: null,
            name: 'api-permission-'.time(),
            guard_name: 'api',
            created_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('api', $result->guard_name);
        $this->assertDatabaseHas('permissions', [
            'name' => $result->name,
            'guard_name' => 'api',
        ]);
    }

    public function test_execute_creates_multiple_permissions(): void
    {
        // Arrange
        $initialCount = Permission::count();
        $repository = new PermissionRepository();
        $action = new CreatePermissionAction($repository);

        $dto1 = new PermissionDTO(id: null, name: 'permission-alpha-'.time(), guard_name: 'web', created_at: null);
        $dto2 = new PermissionDTO(id: null, name: 'permission-beta-'.time(), guard_name: 'web', created_at: null);

        // Act
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        // Assert
        $this->assertStringStartsWith('permission-alpha-', $result1->name);
        $this->assertStringStartsWith('permission-beta-', $result2->name);
        $this->assertEquals($initialCount + 2, Permission::count());
    }

    public function test_execute_creates_permission_from_array(): void
    {
        // Arrange
        $repository = new PermissionRepository();
        $action = new CreatePermissionAction($repository);

        $dto = PermissionDTO::fromArray([
            'name' => 'array-permission-'.time(),
            'guard_name' => 'web',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertStringStartsWith('array-permission-', $result->name);
    }
}
