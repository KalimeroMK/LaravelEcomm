<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Enable2FAAction;
use Modules\Google2fa\Actions\GenerateRecoveryCodesAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;
use Tests\Unit\Actions\ActionTestCase;

class Enable2FAActionTest extends ActionTestCase
{
    private Enable2FAAction $action;
    private GenerateRecoveryCodesAction $recoveryCodesAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recoveryCodesAction = new GenerateRecoveryCodesAction();
        $this->action = new Enable2FAAction($this->recoveryCodesAction);
    }

    public function test_execute_enables_2fa_with_valid_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();
        $validCode = $google2fa->getCurrentOtp($secret);

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => false,
            'google2fa_secret' => $secret,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, $validCode);

        // Assert
        $this->assertTrue($result);
        $this->assertTrue($loginSecurity->fresh()->google2fa_enable);
        $this->assertDatabaseHas('login_securities', [
            'id' => $loginSecurity->id,
            'google2fa_enable' => true,
        ]);
    }

    public function test_execute_returns_false_with_invalid_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => false,
            'google2fa_secret' => $secret,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, '000000');

        // Assert
        $this->assertFalse($result);
        $this->assertFalse($loginSecurity->fresh()->google2fa_enable);
    }

    public function test_execute_generates_recovery_codes_on_success(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();
        $validCode = $google2fa->getCurrentOtp($secret);

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => false,
            'google2fa_secret' => $secret,
            'recovery_codes' => null,
        ]);

        // Act
        $this->action->execute($loginSecurity, $validCode);

        // Assert
        $freshLoginSecurity = $loginSecurity->fresh();
        $this->assertNotNull($freshLoginSecurity->recovery_codes);
        $this->assertIsArray($freshLoginSecurity->recovery_codes);
        $this->assertNotEmpty($freshLoginSecurity->recovery_codes);
    }

    public function test_execute_does_not_enable_with_empty_code(): void
    {
        // Arrange
        $google2fa = new PragmaRXGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => false,
            'google2fa_secret' => $secret,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, '');

        // Assert
        $this->assertFalse($result);
        $this->assertFalse($loginSecurity->fresh()->google2fa_enable);
    }
}
