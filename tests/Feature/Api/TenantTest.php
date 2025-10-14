<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user for authenticated tests
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test tenant route without authentication.
     */
    #[Test]
    public function test_tenant_without_auth(): void
    {
        $response = $this->json('GET', '/api/v1/tenant');

        $response->assertStatus(401);
    }

    /**
     * test tenant route with authentication.
     */
    #[Test]
    public function test_tenant_with_auth(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/tenant');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);
    }
}
