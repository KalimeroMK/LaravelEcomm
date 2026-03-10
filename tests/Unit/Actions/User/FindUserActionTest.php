<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\User\Actions\FindUserAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindUserActionTest extends ActionTestCase
{
    public function testExecuteFindsUserSuccessfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $action = app(FindUserAction::class);
        $result = $action->execute($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function testExecuteReturnsCorrectUserFromMultipleUsers(): void
    {
        User::factory()->count(5)->create();
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
        ]);

        $action = app(FindUserAction::class);
        $result = $action->execute($targetUser->id);

        $this->assertEquals($targetUser->id, $result->id);
        $this->assertEquals('Target User', $result->name);
        $this->assertEquals('target@example.com', $result->email);
    }

    public function testExecuteThrowsExceptionForNonExistentUser(): void
    {
        $action = app(FindUserAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999);
    }

    public function testExecuteThrowsExceptionForDeletedUser(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;
        $user->delete();

        $action = app(FindUserAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute($userId);
    }
}
