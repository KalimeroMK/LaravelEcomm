<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Verify2FAAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;
use Tests\Unit\Actions\ActionTestCase;

class Verify2FAActionTest extends ActionTestCase
{
    private Verify2FAAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Verify2FAAction();
    }

    public function test_execute_returns_true_with_valid_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();
        $validCode = $google2fa->getCurrentOtp($secret);

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => $secret,
            'google2fa_enable' => true,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $validCode);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_false_with_invalid_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => $secret,
            'google2fa_enable' => true,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, '000000');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_returns_false_with_empty_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => $secret,
            'google2fa_enable' => true,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, '');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_verifies_code_within_time_window(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => $secret,
            'google2fa_enable' => true,
        ]);

        // Use current OTP
        $currentCode = $google2fa->getCurrentOtp($secret);

        // Act
        $result = $this->action->execute($loginSecurity, $currentCode);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_is_case_insensitive_for_secret(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();
        $validCode = $google2fa->getCurrentOtp($secret);

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_secret' => strtolower($secret),
            'google2fa_enable' => true,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $validCode);

        // Assert
        $this->assertTrue($result);
    }
}
