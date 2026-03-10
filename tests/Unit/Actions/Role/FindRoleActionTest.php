<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Role\Actions\FindRoleAction;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindRoleActionTest extends ActionTestCase
{
    public function test_execute_finds_role_by_id(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'findable-role',
            'guard_name' => 'web',
        ]);

        $repository = new RoleRepository();
        $action = new FindRoleAction($repository);

        // Act
        $result = $action->execute($role->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($role->id, $result->id);
        $this->assertEquals('findable-role', $result->name);
        $this->assertEquals('web', $result->guard_name);
    }

    public function test_execute_finds_role_with_all_attributes(): void
    {
        // Arrange
        $role = Role::create([
            'name' => 'complete-role',
            'guard_name' => 'api',
        ]);

        $repository = new RoleRepository();
        $action = new FindRoleAction($repository);

        // Act
        $result = $action->execute($role->id);

        // Assert
        $this->assertEquals('complete-role', $result->name);
        $this->assertEquals('api', $result->guard_name);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public function test_execute_throws_exception_for_nonexistent_role(): void
    {
        // Arrange
        $repository = new RoleRepository();
        $action = new FindRoleAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }
}
