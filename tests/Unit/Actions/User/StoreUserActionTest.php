<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\UniqueConstraintViolationException;
use Modules\User\Actions\StoreUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class StoreUserActionTest extends ActionTestCase
{
    public function testExecuteCreatesUserSuccessfully(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'New User',
            email: 'newuser@example.com',
            email_verified_at: now()->toDateTimeString(),
            created_at: now()->toDateTimeString(),
            updated_at: now()->toDateTimeString(),
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('New User', $result->name);
        $this->assertEquals('newuser@example.com', $result->email);
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    public function testExecuteCreatesUserWithNullEmailVerifiedAt(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Unverified User',
            email: 'unverified@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Unverified User', $result->name);
        $this->assertNull($result->email_verified_at);
    }

    public function testExecuteHashesPassword(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'User With Password',
            email: 'password@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->password);
        // Password should be hashed (not plain text)
        $this->assertNotEquals('test-password-123', $result->password);
        $this->assertTrue(password_verify('test-password-123', $result->password));
        // Regression: creation used to ignore the DTO and hardcode 'password'.
        $this->assertFalse(password_verify('password', $result->password));
    }

    public function testExecuteRejectsAMissingPassword(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'No Password User',
            email: 'nopassword@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: null,
        );

        $this->expectException(\InvalidArgumentException::class);
        app(StoreUserAction::class)->execute($dto);
    }

    public function testExecuteThrowsExceptionForDuplicateEmail(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $dto = new UserDTO(
            id: null,
            name: 'Duplicate User',
            email: 'duplicate@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);

        $this->expectException(UniqueConstraintViolationException::class);
        $action->execute($dto);
    }

    public function testExecuteCreatesMultipleUsers(): void
    {
        $dto1 = new UserDTO(
            id: null,
            name: 'User One',
            email: 'user1@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $dto2 = new UserDTO(
            id: null,
            name: 'User Two',
            email: 'user2@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        $this->assertInstanceOf(User::class, $result1);
        $this->assertInstanceOf(User::class, $result2);
        $this->assertNotEquals($result1->id, $result2->id);

        $this->assertDatabaseHas('users', ['email' => 'user1@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'user2@example.com']);
    }

    public function testExecuteReturnsUserWithId(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'testid@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: 'test-password-123',
        );

        $action = app(StoreUserAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->id);
        $this->assertIsInt($result->id);
    }
}
