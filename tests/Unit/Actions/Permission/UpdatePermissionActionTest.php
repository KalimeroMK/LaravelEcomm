<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Permission;

use Illuminate\Database\Eloquent\Model;
use Modules\Permission\Actions\UpdatePermissionAction;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdatePermissionActionTest extends ActionTestCase
{
    public function test_execute_updates_permission_with_dto(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'old-permission-name',
            'guard_name' => 'web',
        ]);

        $repository = new PermissionRepository();
        $action = new UpdatePermissionAction($repository);

        $dto = new PermissionDTO(
            id: $permission->id,
            name: 'new-permission-name',
            guard_name: 'web',
            created_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('new-permission-name', $result->name);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'new-permission-name',
        ]);
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
            'name' => 'old-permission-name',
        ]);
    }

    public function test_execute_updates_permission_preserving_guard(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'api-permission',
            'guard_name' => 'api',
        ]);

        $repository = new PermissionRepository();
        $action = new UpdatePermissionAction($repository);

        $dto = new PermissionDTO(
            id: $permission->id,
            name: 'updated-api-permission',
            guard_name: 'api',
            created_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('updated-api-permission', $result->name);
        $this->assertEquals('api', $result->guard_name);
    }

    public function test_execute_updates_permission_changes_guard(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'change-guard-permission',
            'guard_name' => 'web',
        ]);

        $repository = new PermissionRepository();
        $action = new UpdatePermissionAction($repository);

        $dto = new PermissionDTO(
            id: $permission->id,
            name: 'change-guard-permission',
            guard_name: 'api',
            created_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('api', $result->guard_name);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'guard_name' => 'api',
        ]);
    }

    public function test_execute_updates_multiple_times(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'original-name',
            'guard_name' => 'web',
        ]);

        $repository = new PermissionRepository();
        $action = new UpdatePermissionAction($repository);

        // First update
        $dto1 = new PermissionDTO(id: $permission->id, name: 'first-update', guard_name: 'web', created_at: null);
        $result1 = $action->execute($dto1);
        $this->assertEquals('first-update', $result1->name);

        // Second update
        $dto2 = new PermissionDTO(id: $permission->id, name: 'second-update', guard_name: 'web', created_at: null);
        $result2 = $action->execute($dto2);
        $this->assertEquals('second-update', $result2->name);

        // Assert final state
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'second-update',
        ]);
    }
}
