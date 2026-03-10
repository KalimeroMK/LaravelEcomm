<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Illuminate\Support\Collection;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllRolesActionTest extends ActionTestCase
{
    public function test_execute_returns_collection_of_roles(): void
    {
        // Arrange - get initial count
        $initialCount = Role::count();
        Role::create(['name' => 'role-1-'.time(), 'guard_name' => 'web']);
        Role::create(['name' => 'role-2-'.time(), 'guard_name' => 'web']);
        Role::create(['name' => 'role-3-'.time(), 'guard_name' => 'web']);

        $repository = new RoleRepository();
        $action = new GetAllRolesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($initialCount + 3, $result);
    }

    public function test_execute_returns_collection(): void
    {
        // Arrange - database already has seeded roles
        $repository = new RoleRepository();
        $action = new GetAllRolesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count());
    }

    public function test_execute_returns_role_models(): void
    {
        // Arrange - create a role and verify it exists in the collection
        $roleName = 'test-role-'.time();
        Role::create(['name' => $roleName, 'guard_name' => 'web']);

        $repository = new RoleRepository();
        $action = new GetAllRolesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Role::class, $result->first());
        $this->assertTrue($result->contains('name', $roleName));
        $foundRole = $result->firstWhere('name', $roleName);
        $this->assertEquals('web', $foundRole->guard_name);
    }

    public function test_execute_includes_roles_with_different_guards(): void
    {
        // Arrange - get initial count
        $initialCount = Role::count();
        Role::create(['name' => 'web-role-'.time(), 'guard_name' => 'web']);
        Role::create(['name' => 'api-role-'.time(), 'guard_name' => 'api']);

        $repository = new RoleRepository();
        $action = new GetAllRolesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount($initialCount + 2, $result);
    }
}
