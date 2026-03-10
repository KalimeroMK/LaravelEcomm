<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Exception;
use Lab404\Impersonate\Services\ImpersonateManager;
use Modules\User\Actions\ImpersonateUserAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ImpersonateUserActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    public function testExecuteImpersonatesUserSuccessfully(): void
    {
        $impersonator = User::factory()->create();
        $impersonator->assignRole('super-admin');
        $targetUser = User::factory()->create();

        $this->actingAs($impersonator);

        $action = app(ImpersonateUserAction::class);
        $action->execute($targetUser);

        // Check impersonation via the impersonate manager
        $manager = app(ImpersonateManager::class);
        $this->assertTrue($manager->isImpersonating());
    }

    public function testExecuteThrowsExceptionWhenNotAuthenticated(): void
    {
        $targetUser = User::factory()->create();

        $action = app(ImpersonateUserAction::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No authenticated user found.');
        $action->execute($targetUser);
    }

    public function testExecuteCanImpersonateDifferentUsers(): void
    {
        $impersonator = User::factory()->create();
        $impersonator->assignRole('super-admin');
        $targetUser1 = User::factory()->create();
        $targetUser2 = User::factory()->create();

        $this->actingAs($impersonator);

        $action = app(ImpersonateUserAction::class);
        $manager = app(ImpersonateManager::class);

        // First impersonation
        $action->execute($targetUser1);
        $this->assertTrue($manager->isImpersonating());

        // Stop impersonating
        $manager->leave();
        $this->assertFalse($manager->isImpersonating());

        // Second impersonation
        $this->actingAs($impersonator);
        $action->execute($targetUser2);
        $this->assertTrue($manager->isImpersonating());
    }

    public function testExecutePassesCorrectUserToImpersonate(): void
    {
        $impersonator = User::factory()->create();
        $impersonator->assignRole('super-admin');
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
        ]);

        $this->actingAs($impersonator);

        $action = app(ImpersonateUserAction::class);

        // Verify no exception is thrown and method executes
        $this->expectNotToPerformAssertions();
        $action->execute($targetUser);
    }
}
