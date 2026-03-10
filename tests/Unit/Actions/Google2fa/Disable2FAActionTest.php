<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Google2fa;

use Modules\Google2fa\Actions\Disable2FAAction;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class Disable2FAActionTest extends ActionTestCase
{
    private Disable2FAAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new Disable2FAAction();
    }

    public function test_execute_disables_2fa_for_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $google2fa = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => true,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertInstanceOf(Google2fa::class, $result);
        $this->assertFalse($result->google2fa_enable);
        $this->assertDatabaseHas('login_securities', [
            'user_id' => $user->id,
            'google2fa_enable' => false,
        ]);
    }

    public function test_execute_creates_new_record_if_not_exists(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertInstanceOf(Google2fa::class, $result);
        $this->assertFalse($result->google2fa_enable);
        $this->assertDatabaseHas('login_securities', [
            'user_id' => $user->id,
            'google2fa_enable' => false,
        ]);
    }

    public function test_execute_preserves_secret_key(): void
    {
        // Arrange
        $user = User::factory()->create();
        $secret = 'test-secret-key';
        Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => true,
            'google2fa_secret' => $secret,
        ]);

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertEquals($secret, $result->google2fa_secret);
    }

    public function test_execute_returns_existing_model_if_already_disabled(): void
    {
        // Arrange
        $user = User::factory()->create();
        $google2fa = Google2fa::factory()->create([
            'user_id' => $user->id,
            'google2fa_enable' => false,
            'google2fa_secret' => 'test-secret',
        ]);

        // Act
        $result = $this->action->execute($user);

        // Assert
        $this->assertInstanceOf(Google2fa::class, $result);
        $this->assertFalse($result->google2fa_enable);
    }
}
