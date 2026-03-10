<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\GenerateRecoveryCodesAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\Google2fa\Models\Google2faSetting;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GenerateRecoveryCodesActionTest extends ActionTestCase
{
    private GenerateRecoveryCodesAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GenerateRecoveryCodesAction();
    }

    public function test_execute_generates_recovery_codes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => true,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(10, $result); // Default count is 10

        // Check format: XXXXXXXX-XXXXXXXX
        foreach ($result as $code) {
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}-[A-Z0-9]{8}$/', $code);
        }
    }

    public function test_execute_saves_codes_to_model(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => null,
        ]);

        // Act
        $codes = $this->action->execute($loginSecurity);

        // Assert
        $freshLoginSecurity = $loginSecurity->fresh();
        $this->assertNotNull($freshLoginSecurity->recovery_codes);
        $this->assertEquals($codes, $freshLoginSecurity->recovery_codes);
    }

    public function test_execute_uses_custom_count_from_settings(): void
    {
        // Arrange
        $settings = Google2faSetting::getSettings();
        $settings->update(['recovery_codes_count' => 5]);

        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(5, $result);
    }

    public function test_execute_generates_unique_codes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity);

        // Assert
        $uniqueCodes = array_unique($result);
        $this->assertCount(count($result), $uniqueCodes);
    }

    public function test_execute_replaces_existing_codes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $oldCodes = ['OLDCODE1-XXXXXXXX', 'OLDCODE2-XXXXXXXX'];
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
            'recovery_codes' => $oldCodes,
        ]);

        // Act
        $newCodes = $this->action->execute($loginSecurity);

        // Assert
        $this->assertNotEquals($oldCodes, $newCodes);
        $freshLoginSecurity = $loginSecurity->fresh();
        $this->assertEquals($newCodes, $freshLoginSecurity->recovery_codes);
    }

    public function test_execute_returns_uppercase_codes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $loginSecurity = Google2fa::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act
        $result = $this->action->execute($loginSecurity);

        // Assert
        foreach ($result as $code) {
            $this->assertEquals(mb_strtoupper($code), $code);
        }
    }
}
