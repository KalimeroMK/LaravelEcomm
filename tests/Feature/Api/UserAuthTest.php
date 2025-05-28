<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the 'client' role exists for the web guard
        if (!Role::where('name', 'client')->where('guard_name', 'web')->exists()) {
            Role::create([
                'name' => 'client',
                'guard_name' => 'web',
            ]);
        }
    }

    #[Test]
    public function user_can_register(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/register', $data);
        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['token'],
        ]);
    }

    #[Test]
    public function user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['token'],
        ]);
    }

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/logout');

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);
    }
}
