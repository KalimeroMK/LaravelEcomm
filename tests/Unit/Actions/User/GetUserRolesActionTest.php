<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\User\Actions\GetUserRolesAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetUserRolesActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    public function testExecuteReturnsEmptyArrayForUserWithNoRoles(): void
    {
        $user = User::factory()->create();
        // Ensure user has no roles
        $user->syncRoles([]);

        $action = app(GetUserRolesAction::class);
        $result = $action->execute($user->id);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testExecuteReturnsRoleIdsForUserWithSingleRole(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $action = app(GetUserRolesAction::class);
        $result = $action->execute($user->id);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContains($user->roles->first()->id, $result);
    }

    public function testExecuteReturnsRoleIdsForUserWithMultipleRoles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['client', 'admin']);

        $action = app(GetUserRolesAction::class);
        $result = $action->execute($user->id);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $expectedRoleIds = $user->roles->pluck('id')->all();
        $this->assertEquals($expectedRoleIds, $result);
    }

    public function testExecuteReturnsOnlyRoleIdsNotNames(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $action = app(GetUserRolesAction::class);
        $result = $action->execute($user->id);

        $this->assertIsArray($result);
        foreach ($result as $roleId) {
            $this->assertIsInt($roleId);
        }
    }

    public function testExecuteThrowsExceptionForNonExistentUser(): void
    {
        $action = app(GetUserRolesAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999);
    }
}
