<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Illuminate\Database\UniqueConstraintViolationException;
use InvalidArgumentException;
use Modules\User\Actions\RegisterUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class RegisterUserActionTest extends ActionTestCase
{
    private const SUBMITTED_PASSWORD = 'c0rrect-horse-battery';

    public function testExecuteCreatesNewUser(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'newuser@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: self::SUBMITTED_PASSWORD,
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('newuser@example.com', $result->email);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function testExecuteStoresTheSubmittedPasswordHashed(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'passwordtest@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: self::SUBMITTED_PASSWORD,
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->password);
        $this->assertNotSame(self::SUBMITTED_PASSWORD, $result->password, 'Password must not be stored in plain text.');
        $this->assertTrue(password_verify(self::SUBMITTED_PASSWORD, $result->password));
    }

    /**
     * Regression: registration used to ignore the submitted password and fall
     * back to a hardcoded 'password', making every account trivially guessable.
     */
    public function testExecuteNeverFallsBackToADefaultPassword(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'nodefault@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: self::SUBMITTED_PASSWORD,
        );

        $result = app(RegisterUserAction::class)->execute($dto);

        $this->assertFalse(password_verify('password', $result->password));
    }

    public function testExecuteRejectsAMissingPassword(): void
    {
        $dto = new UserDTO(
            id: null,
            name: 'Test User',
            email: 'nopassword@example.com',
            email_verified_at: null,
            created_at: null,
            updated_at: null,
            password: null,
        );

        $this->expectException(InvalidArgumentException::class);
        app(RegisterUserAction::class)->execute($dto);
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
            password: self::SUBMITTED_PASSWORD,
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
            password: self::SUBMITTED_PASSWORD,
        );

        $action = app(RegisterUserAction::class);

        // Database throws unique constraint violation
        $this->expectException(UniqueConstraintViolationException::class);
        $action->execute($dto);
    }
}
