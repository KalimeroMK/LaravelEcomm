<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Modules\User\Actions\DeleteUserAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeleteUserActionTest extends ActionTestCase
{
    public function testExecuteDeletesUserSuccessfully(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $action = app(DeleteUserAction::class);
        $action->execute($user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testExecuteDeletesSpecificUserOnly(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $action = app(DeleteUserAction::class);
        $action->execute($user2->id);

        $this->assertDatabaseHas('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
        $this->assertDatabaseHas('users', ['id' => $user3->id]);
    }

    public function testExecuteDoesNothingForNonExistentUser(): void
    {
        // The repository destroy method does not throw an exception for non-existent IDs
        // It simply returns without doing anything
        $action = app(DeleteUserAction::class);

        // Should not throw any exception
        $action->execute(99999);

        $this->assertTrue(true); // Test passes if we reach here
    }
}
