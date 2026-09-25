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
        $seeded = User::count();

        // Create 3 new users
        User::factory()->count(3)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(UserListDTO::class, $result);
        // Seeded users (count varies with the seeders) + 3 created users
        $this->assertCount($seeded + 3, $result->users);
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
        $seeded = User::count();

        User::factory()->count(5)->create();

        $action = app(GetAllUsersAction::class);
        $result = $action->execute();

        // Seeded users (count varies with the seeders) + 5 created users
        $this->assertCount($seeded + 5, $result->users);
    }
}
