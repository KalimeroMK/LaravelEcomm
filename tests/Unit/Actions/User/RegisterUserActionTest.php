<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\UniqueConstraintViolationException;
use Modules\User\Actions\RegisterUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class RegisterUserActionTest extends ActionTestCase
{
    public function testExecuteCreatesNewUser(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'newuser@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('newuser@example.com', $result->email);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function testExecuteCreatesUserWithDefaultPassword(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'passwordtest@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        // Password should be hashed (defaults to 'password')
        $this->assertNotNull($result->password);
        $this->assertTrue(password_verify('password', $result->password));
    }

    public function testExecuteCreatesUserWithVerifiedEmail(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Verified User',
            email: 'verified@example.com',
            email_verified_at: now()->toDateTimeString(),
            created_at: null,
            updated_at: null,
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->email_verified_at);
    }

    public function testExecuteThrowsExceptionForDuplicateEmail(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'duplicate@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
        );

        $action = app(RegisterUserAction::class);

        // Database throws unique constraint violation
        $this->expectException(UniqueConstraintViolationException::class);
        $action->execute($dto);
    }
}
