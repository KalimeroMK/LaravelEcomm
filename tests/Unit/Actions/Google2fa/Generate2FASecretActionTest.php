<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Generate2FASecretAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class Generate2FASecretActionTest extends ActionTestCase
{
    private Generate2FASecretAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Generate2FASecretAction();
    }

    public function test_execute_creates_new_2fa_record(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertInstanceOf(Google2fa::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertFalse($result->google2fa_enable);
        $this->assertNotNull($result->google2fa_secret);
        $this->assertDatabaseHas('login_securities', [
            'user_id' => $user->id,
            'google2fa_enable' => false,
        ]);
    }

    public function test_execute_generates_unique_secret_key(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Act
        $result1 = $this->action->execute($user1);
        $result2 = $this->action->execute($user2);

        // Assert
        $this->assertNotEquals($result1->google2fa_secret, $result2->google2fa_secret);
        $this->assertNotEmpty($result1->google2fa_secret);
        $this->assertNotEmpty($result2->google2fa_secret);
    }

    public function test_execute_resets_existing_enabled_2fa(): void
    {
        // Arrange
        $user = User::factory()->create();
        $existing = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => true,
            'google2fa_secret' => 'old-secret',
        ]);

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertFalse($result->google2fa_enable);
        $this->assertNotEquals('old-secret', $result->google2fa_secret);
    }

    public function test_execute_generates_valid_base32_secret(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $secret = $result->google2fa_secret;
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
        $this->assertGreaterThanOrEqual(16, strlen($secret));
    }

    public function test_execute_preserves_user_id(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertEquals($user->id, $result->user_id);
    }
}
