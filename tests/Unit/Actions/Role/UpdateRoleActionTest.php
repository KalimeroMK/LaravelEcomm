<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Illuminate\Database\Eloquent\Model;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateRoleActionTest extends ActionTestCase
{
    public function test_execute_updates_role_with_dto(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'old-role-name',
            'guard_name' => 'web',
        ]);

        $repository = new RoleRepository();
        $action = new UpdateRoleAction($repository);

        $dto = new RoleDTO(
            id: $role->id,
            name: 'new-role-name',
        );

        // Act
        $result = $action->execute($role->id, $dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals('new-role-name', $result->name);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'new-role-name',
        ]);
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
            'name' => 'old-role-name',
        ]);
    }

    public function test_execute_updates_role_preserving_guard(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'api-role',
            'guard_name' => 'api',
        ]);

        $repository = new RoleRepository();
        $action = new UpdateRoleAction($repository);

        $dto = new RoleDTO(
            id: $role->id,
            name: 'updated-api-role',
        );

        // Act
        $result = $action->execute($role->id, $dto);

        // Assert
        $this->assertEquals('updated-api-role', $result->name);
        $this->assertEquals('api', $result->guard_name);
    }

    public function test_execute_updates_role_with_permissions(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'role-to-update',
            'guard_name' => 'web',
        ]);

        $repository = new RoleRepository();
        $action = new UpdateRoleAction($repository);

        $dto = new RoleDTO(
            id: $role->id,
            name: 'updated-role-name',
            permissions: ['view-users', 'edit-users'],
        );

        // Act
        $result = $action->execute($role->id, $dto);

        // Assert
        $this->assertEquals('updated-role-name', $result->name);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role-name',
        ]);
    }

    public function test_execute_updates_multiple_times(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'original-name',
            'guard_name' => 'web',
        ]);

        $repository = new RoleRepository();
        $action = new UpdateRoleAction($repository);

        // First update
        $dto1 = new RoleDTO(id: $role->id, name: 'first-update');
        $result1 = $action->execute($role->id, $dto1);
        $this->assertEquals('first-update', $result1->name);

        // Second update
        $dto2 = new RoleDTO(id: $role->id, name: 'second-update');
        $result2 = $action->execute($role->id, $dto2);
        $this->assertEquals('second-update', $result2->name);

        // Assert final state
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'second-update',
        ]);
    }
}
