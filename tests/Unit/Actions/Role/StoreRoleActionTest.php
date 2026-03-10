<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;
use Tests\Unit\Actions\ActionTestCase;

class StoreRoleActionTest extends ActionTestCase
{
    public function test_execute_creates_role_with_dto(): void
    {
        // Arrange
        $repository = new RoleRepository();
        $action = new StoreRoleAction($repository);

        $dto = new RoleDTO(
            id: null,
            name: 'new-role-'.time(),
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertStringStartsWith('new-role-', $result->name);
        $this->assertDatabaseHas('roles', [
            'name' => $result->name,
            'guard_name' => 'web', // default guard
        ]);
    }

    public function test_execute_creates_role_with_permissions(): void
    {
        // Arrange
        $repository = new RoleRepository();
        $action = new StoreRoleAction($repository);

        $dto = new RoleDTO(
            id: null,
            name: 'role-with-permissions-'.time(),
            permissions: ['permission-1', 'permission-2'],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertStringStartsWith('role-with-permissions-', $result->name);
        $this->assertDatabaseHas('roles', [
            'name' => $result->name,
        ]);
    }

    public function test_execute_creates_multiple_roles(): void
    {
        // Arrange
        $initialCount = Role::count();
        $repository = new RoleRepository();
        $action = new StoreRoleAction($repository);

        $dto1 = new RoleDTO(id: null, name: 'role-alpha-'.time());
        $dto2 = new RoleDTO(id: null, name: 'role-beta-'.time());

        // Act
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        // Assert
        $this->assertStringStartsWith('role-alpha-', $result1->name);
        $this->assertStringStartsWith('role-beta-', $result2->name);
        $this->assertEquals($initialCount + 2, Role::count());
    }

    public function test_execute_creates_role_from_array(): void
    {
        // Arrange
        $repository = new RoleRepository();
        $action = new StoreRoleAction($repository);

        $dto = RoleDTO::fromArray([
            'name' => 'array-role-'.time(),
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertStringStartsWith('array-role-', $result->name);
    }
}
