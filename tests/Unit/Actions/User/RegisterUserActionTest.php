<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Modules\User\Actions\RegisterUserAction;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class RegisterUserActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
    }

    public function testExecuteCreatesNewUser(): void
    {
        $dto = new UserDTO(
            name: 'Test User',
            email: 'newuser@example.com',
            password: 'securepassword123',
            passwordConfirmation: 'securepassword123',
            roles: ['client'],
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('newuser@example.com', $result->email);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function testExecuteAssignsDefaultClientRole(): void
    {
        $dto = new UserDTO(
            name: 'Test User',
            email: 'roletest@example.com',
            password: 'securepassword123',
            passwordConfirmation: 'securepassword123',
            roles: [],
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertTrue($result->hasRole('client'));
    }

    public function testExecuteHashesPassword(): void
    {
        $dto = new UserDTO(
            name: 'Test User',
            email: 'hashtest@example.com',
            password: 'mypassword123',
            passwordConfirmation: 'mypassword123',
            roles: [],
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertNotEquals('mypassword123', $result->password);
        $this->assertTrue(password_verify('mypassword123', $result->password));
    }

    public function testExecuteAssignsSpecifiedRoles(): void
    {
        $dto = new UserDTO(
            name: 'Test User',
            email: 'roletest2@example.com',
            password: 'securepassword123',
            passwordConfirmation: 'securepassword123',
            roles: ['client', 'manager'],
        );

        $action = app(RegisterUserAction::class);
        $result = $action->execute($dto);

        $this->assertTrue($result->hasRole('client'));
        $this->assertTrue($result->hasRole('manager'));
    }
}
