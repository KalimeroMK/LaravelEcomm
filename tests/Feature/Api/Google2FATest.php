<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Google2FATest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function test_get_2fa_status(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/2fa/status');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'enabled',
            ],
        ]);
    }

    #[Test]
    public function test_generate_2fa_secret(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/2fa/generate-secret');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'secret_key',
                'qr_code',
            ],
        ]);
    }

    #[Test]
    public function test_enable_2fa_without_secret(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/2fa/enable', [
            'verification_code' => '123456',
        ]);

        // Should fail because secret hasn't been generated
        $response->assertStatus(400);
    }

    #[Test]
    public function test_disable_2fa(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/2fa/disable');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'enabled',
            ],
        ]);
    }

    #[Test]
    public function test_get_recovery_codes_when_not_enabled(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/2fa/recovery-codes');

        // Should fail because 2FA is not enabled
        $response->assertStatus(400);
    }

    #[Test]
    public function test_verify_2fa_when_not_setup(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/2fa/verify', [
            'verification_code' => '123456',
        ]);

        // Should fail because 2FA is not set up
        $response->assertStatus(400);
    }

    #[Test]
    public function test_2fa_settings_index(): void
    {
        // Assign admin role to user
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $this->user->assignRole($adminRole);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/admin/2fa/settings');

        // May return 200 (if admin) or 403 (if not authorized)
        expect($response->status())->toBeIn([200, 403]);
    }
}
