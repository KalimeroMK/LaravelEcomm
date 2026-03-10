<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\User\Actions\UpdateUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateUserActionTest extends ActionTestCase
{
    public function testExecuteUpdatesUserSuccessfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $dto = new UserDTO(
            id: $user->id,
            name: 'New Name',
            email: 'new@example.com',
            email_verified_at: $user->email_verified_at?->toDateTimeString(),
            created_at: $user->created_at?->toDateTimeString(),
            updated_at: now()->toDateTimeString(),
        );

        $action = app(UpdateUserAction::class);
        $result = $action->execute($user->id, $dto);

        $this->assertEquals('New Name', $result->name);
        $this->assertEquals('new@example.com', $result->email);
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function testExecuteUpdatesUserNameOnly(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'same@example.com',
        ]);

        $dto = new UserDTO(
            id: $user->id,
            name: 'New Name Only',
            email: $user->email,
            email_verified_at: $user->email_verified_at?->toDateTimeString(),
            created_at: $user->created_at?->toDateTimeString(),
            updated_at: now()->toDateTimeString(),
        );

        $action = app(UpdateUserAction::class);
        $result = $action->execute($user->id, $dto);

        $this->assertEquals('New Name Only', $result->name);
        $this->assertEquals('same@example.com', $result->email);
    }

    public function testExecuteThrowsExceptionForNonExistentUser(): void
    {
        $dto = new UserDTO(
            id: 99999,
            name: 'Test',
            email: 'test@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
        );

        $action = app(UpdateUserAction::class);
        
        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999, $dto);
    }

    public function testExecuteThrowsExceptionForDuplicateEmail(): void
    {
        $user1 = User::factory()->create(['email' => 'existing@example.com']);
        $user2 = User::factory()->create(['email' => 'original@example.com']);

        $dto = new UserDTO(
            id: $user2->id,
            name: $user2->name,
            email: 'existing@example.com', // Try to use user1's email
            email_verified_at: $user2->email_verified_at?->toDateTimeString(),
            created_at: $user2->created_at?->toDateTimeString(),
            updated_at: now()->toDateTimeString(),
        );

        $action = app(UpdateUserAction::class);
        
        // Database will throw unique constraint violation
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        $action->execute($user2->id, $dto);
    }
}
