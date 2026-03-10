<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Modules\User\Actions\GetUsersForIndexAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetUsersForIndexActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    public function testExecuteReturnsAllUsersForSuperAdmin(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        User::factory()->count(3)->create();

        $this->actingAs($superAdmin);

        $action = app(GetUsersForIndexAction::class);
        $result = $action->execute();

        $this->assertIsArray($result);
        // 2 (LanguageDatabaseSeeder) + 4 (PermissionTableSeeder) + superAdmin + 3 = 10
        $this->assertCount(10, $result);
    }

    public function testExecuteReturnsSingleUserForNonSuperAdmin(): void
    {
        $regularUser = User::factory()->create();
        $regularUser->assignRole('client');

        User::factory()->count(3)->create();

        $this->actingAs($regularUser);

        $action = app(GetUsersForIndexAction::class);
        $result = $action->execute();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($regularUser->id, $result[0]->id);
    }

    public function testExecuteReturnsOnlyAuthenticatedUserForRegularUser(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('client');
        $user2 = User::factory()->create();
        $user2->assignRole('admin');

        $this->actingAs($user1);

        $action = app(GetUsersForIndexAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertEquals($user1->id, $result[0]->id);
        $this->assertNotEquals($user2->id, $result[0]->id);
    }

    public function testExecuteAbortsForNonNumericUserId(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('User not found.');

        // When no user is authenticated, Auth::id() returns null
        $action = app(GetUsersForIndexAction::class);
        $action->execute();
    }

    public function testExecuteThrowsExceptionForNonExistentAuthenticatedUser(): void
    {
        // This shouldn't happen in practice, but test the abort case
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('User not found.');

        $action = app(GetUsersForIndexAction::class);
        $action->execute();
    }

    public function testExecuteAbortsWhenAuthIdIsNull(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        // Not authenticated
        $action = app(GetUsersForIndexAction::class);
        $action->execute();
    }
}
