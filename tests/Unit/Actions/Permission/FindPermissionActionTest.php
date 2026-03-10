<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Permission\Actions\FindPermissionAction;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repository\PermissionRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindPermissionActionTest extends ActionTestCase
{
    public function test_execute_finds_permission_by_id(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'findable-permission',
            'guard_name' => 'web',
        ]);

        $repository = new PermissionRepository();
        $action = new FindPermissionAction($repository);

        // Act
        $result = $action->execute($permission->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($permission->id, $result->id);
        $this->assertEquals('findable-permission', $result->name);
        $this->assertEquals('web', $result->guard_name);
    }

    public function test_execute_finds_permission_with_all_attributes(): void
    {
        // Arrange
        $permission = Permission::create([
            'name' => 'complete-permission',
            'guard_name' => 'api',
        ]);

        $repository = new PermissionRepository();
        $action = new FindPermissionAction($repository);

        // Act
        $result = $action->execute($permission->id);

        // Assert
        $this->assertEquals('complete-permission', $result->name);
        $this->assertEquals('api', $result->guard_name);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public function test_execute_throws_exception_for_nonexistent_permission(): void
    {
        // Arrange
        $repository = new PermissionRepository();
        $action = new FindPermissionAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }
}
