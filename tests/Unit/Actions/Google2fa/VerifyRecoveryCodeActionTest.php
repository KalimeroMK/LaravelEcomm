<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\VerifyRecoveryCodeAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class VerifyRecoveryCodeActionTest extends ActionTestCase
{
    private VerifyRecoveryCodeAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new VerifyRecoveryCodeAction();
    }

    public function test_execute_returns_true_with_valid_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recoveryCodes = ['VALIDCODE-XXXXXXXX', 'ANOTHER1-YYYYYYYY'];
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => $recoveryCodes,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'VALIDCODE-XXXXXXXX');

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_returns_false_with_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => ['VALIDCODE-XXXXXXXX'],
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'INVALID1-YYYYYYYY');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_removes_used_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => ['USEDCODE-XXXXXXXX', 'REMAIN01-YYYYYYYY'],
        ]);

        // Act
        $this->action->execute($loginSecurity, 'USEDCODE-XXXXXXXX');

        // Assert
        $freshLoginSecurity = $loginSecurity->fresh();
        $this->assertNotContains('USEDCODE-XXXXXXXX', $freshLoginSecurity->recovery_codes);
        $this->assertContains('REMAIN01-YYYYYYYY', $freshLoginSecurity->recovery_codes);
    }

    public function test_execute_returns_false_when_no_recovery_codes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => null,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'ANYCODE1-XXXXXXXX');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_returns_false_with_empty_codes_array(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => [],
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'ANYCODE1-XXXXXXXX');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_is_case_sensitive(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => ['UPPERCASE-XXXXXXXX'],
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'uppercase-xxxxxxxx');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_verifies_code_format(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => ['ABCD1234-EFGH5678'],
        ]);

        // Act
        $result = $this->action->execute($loginSecurity, 'ABCD1234-EFGH5678');

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_handles_partial_code_match(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => ['FULLCODE-XXXXXXXX'],
        ]);

        // Act - Try partial match
        $result = $this->action->execute($loginSecurity, 'FULLCODE');

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_removes_only_used_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $codes = [
            'CODE0001-XXXXXXXX',
            'CODE0002-XXXXXXXX',
            'CODE0003-XXXXXXXX',
        ];
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => $codes,
        ]);

        // Act
        $this->action->execute($loginSecurity, 'CODE0002-XXXXXXXX');

        // Assert
        $freshLoginSecurity = $loginSecurity->fresh();
        $this->assertCount(2, $freshLoginSecurity->recovery_codes);
        $this->assertContains('CODE0001-XXXXXXXX', $freshLoginSecurity->recovery_codes);
        $this->assertNotContains('CODE0002-XXXXXXXX', $freshLoginSecurity->recovery_codes);
        $this->assertContains('CODE0003-XXXXXXXX', $freshLoginSecurity->recovery_codes);
    }
}
