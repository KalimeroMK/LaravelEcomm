<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression coverage for registration password handling.
 *
 * UserDTO had no $password property, so RegisterUserAction's
 * `Hash::make($dto->password ?? 'password')` silently resolved to the literal
 * string 'password' for every account created through /api/v1/register.
 *
 * The pre-existing happy-path test registered with the password "password",
 * so it passed either way. These tests use a distinct password specifically so
 * the two cases cannot be confused.
 */
class RegistrationPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const CHOSEN_PASSWORD = 'Str0ng-Unique-Passphrase';

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'client']);
    }

    #[Test]
    public function test_registration_stores_the_password_the_user_chose(): void
    {
        $email = 'chosen-password@example.com';

        $this->postJson('/api/v1/register', [
            'name' => 'Chosen Password User',
            'email' => $email,
            'password' => self::CHOSEN_PASSWORD,
            'password_confirmation' => self::CHOSEN_PASSWORD,
        ])->assertStatus(200);

        $user = User::where('email', $email)->firstOrFail();

        $this->assertTrue(
            Hash::check(self::CHOSEN_PASSWORD, $user->password),
            'The account was not created with the submitted password.'
        );
    }

    #[Test]
    public function test_registration_does_not_leave_a_guessable_default_password(): void
    {
        $email = 'no-default@example.com';

        $this->postJson('/api/v1/register', [
            'name' => 'No Default User',
            'email' => $email,
            'password' => self::CHOSEN_PASSWORD,
            'password_confirmation' => self::CHOSEN_PASSWORD,
        ])->assertStatus(200);

        $user = User::where('email', $email)->firstOrFail();

        $this->assertFalse(
            Hash::check('password', $user->password),
            'Account is still accessible with the hardcoded default password.'
        );
    }

    #[Test]
    public function test_a_registered_user_can_log_in_with_their_own_password(): void
    {
        $email = 'login-roundtrip@example.com';

        $this->postJson('/api/v1/register', [
            'name' => 'Round Trip User',
            'email' => $email,
            'password' => self::CHOSEN_PASSWORD,
            'password_confirmation' => self::CHOSEN_PASSWORD,
        ])->assertStatus(200);

        $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => self::CHOSEN_PASSWORD,
        ])->assertStatus(200);
    }

    #[Test]
    public function test_a_registered_user_cannot_log_in_with_the_old_default_password(): void
    {
        $email = 'default-login@example.com';

        $this->postJson('/api/v1/register', [
            'name' => 'Default Login User',
            'email' => $email,
            'password' => self::CHOSEN_PASSWORD,
            'password_confirmation' => self::CHOSEN_PASSWORD,
        ])->assertStatus(200);

        $response = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $this->assertNotSame(
            200,
            $response->getStatusCode(),
            'Authentication bypass: the hardcoded default password still works.'
        );
    }
}
