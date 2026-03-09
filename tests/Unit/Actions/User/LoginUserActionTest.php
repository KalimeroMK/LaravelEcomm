<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Modules\User\Actions\LoginUserAction;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class LoginUserActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
    }

    public function testExecuteSuccessfullyLogsInUser(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('client');

        $action = app(LoginUserAction::class);
        $result = $action->execute('test@example.com', 'password123');

        $this->assertNotNull($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($user->name, $result['name']);
    }

    public function testExecuteReturnsNullForInvalidCredentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $action = app(LoginUserAction::class);
        $result = $action->execute('test@example.com', 'wrongpassword');

        $this->assertNull($result);
    }

    public function testExecuteReturnsNullForNonExistentUser(): void
    {
        $action = app(LoginUserAction::class);
        $result = $action->execute('nonexistent@example.com', 'password123');

        $this->assertNull($result);
    }
}
