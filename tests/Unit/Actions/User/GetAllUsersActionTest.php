<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Modules\User\Actions\GetAllUsersAction;
use Modules\User\DTOs\UserListDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllUsersActionTest extends ActionTestCase
{
    public function testExecuteReturnsUserListDTO(): void
    {
        // Create 3 new users
        User::factory()->count(3)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(UserListDTO::class, $result);
        // LanguageDatabaseSeeder creates 2 users + 3 created users = 5
        $this->assertCount(5, $result->users);
    }

    public function testExecuteReturnsListWithSeededUsers(): void
    {
        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(UserListDTO::class, $result);
        // LanguageDatabaseSeeder creates 2 demo users
        $this->assertGreaterThanOrEqual(2, $result->users->count());
    }

    public function testExecuteReturnsUsersOrderedByIdDescending(): void
    {
        User::factory()->count(3)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $userIds = $result->users->pluck('id')->toArray();
        // Verify descending order
        for ($i = 0; $i < count($userIds) - 1; $i++) {
            $this->assertGreaterThan($userIds[$i + 1], $userIds[$i]);
        }
    }

    public function testExecuteIncludesRolesRelationship(): void
    {
        User::factory()->count(2)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        foreach ($result->users as $user) {
            $this->assertTrue($user->relationLoaded('roles'));
        }
    }

    public function testExecuteReturnsCorrectUserCount(): void
    {
        User::factory()->count(5)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        // 2 seeded users + 5 created users = 7
        $this->assertCount(7, $result->users);
    }
}
