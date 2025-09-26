<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpenAITest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

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
     * test openai route without authentication.
     */
    #[Test]
    public function test_openai_without_auth(): void
    {
        $response = $this->json('GET', '/api/v1/openai');
        
        $response->assertStatus(401);
    }

    /**
     * test openai route with authentication.
     */
    #[Test]
    public function test_openai_with_auth(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/openai');
        
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
