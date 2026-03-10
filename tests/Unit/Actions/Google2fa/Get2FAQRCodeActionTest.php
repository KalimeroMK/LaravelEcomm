<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Get2FAQRCodeAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class Get2FAQRCodeActionTest extends ActionTestCase
{
    private Get2FAQRCodeAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Get2FAQRCodeAction();
    }

    public function test_execute_returns_qr_code_data(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'test-secret-key-123',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('qr_code', $result);
        $this->assertArrayHasKey('secret_key', $result);
        $this->assertArrayHasKey('url', $result);
    }

    public function test_execute_returns_correct_secret_key(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'my-secret-key',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email);

        // Assert
        $this->assertEquals('my-secret-key', $result['secret_key']);
    }

    public function test_execute_returns_base64_encoded_svg_qr_code(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email);

        // Assert
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $result['qr_code']);
        
        // Verify it's valid base64
        $base64Data = substr($result['qr_code'], strlen('data:image/svg+xml;base64,'));
        $decoded = base64_decode($base64Data, true);
        $this->assertNotFalse($decoded);
        $this->assertStringContainsString('<svg', $decoded);
    }

    public function test_execute_returns_valid_otp_url(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email);

        // Assert
        $this->assertStringStartsWith('otpauth://totp/', $result['url']);
        $this->assertStringContainsString('test%40example.com', $result['url']); // URL encoded @
        $this->assertStringContainsString('test-secret', $result['url']);
    }

    public function test_execute_uses_custom_app_name(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email, 'CustomApp');

        // Assert
        $this->assertStringContainsString('CustomApp', $result['url']);
    }

    public function test_execute_uses_default_app_name_when_not_provided(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $user->email);

        // Assert
        $this->assertStringContainsString('Kalimero-Ecomm', $result['url']);
    }
}
