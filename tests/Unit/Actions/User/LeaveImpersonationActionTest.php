<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use Exception;
use Lab404\Impersonate\Services\ImpersonateManager;
use Modules\User\Actions\LeaveImpersonationAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class LeaveImpersonationActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    public function testExecuteLeavesImpersonationSuccessfully(): void
    {
        $impersonator = User::factory()->create();
        $impersonator->assignRole('super-admin');
        $targetUser = User::factory()->create();

        $this->actingAs($impersonator);

        // Start impersonating
        $manager = app(ImpersonateManager::class);
        $manager->take($impersonator, $targetUser);
        $this->assertTrue($manager->isImpersonating());

        // Execute leave impersonation action
        $action = app(LeaveImpersonationAction::class);
        $action->execute();

        // Check that impersonation has stopped
        $this->assertFalse($manager->isImpersonating());
    }

    public function testExecuteThrowsExceptionWhenNotAuthenticated(): void
    {
        $action = app(LeaveImpersonationAction::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No authenticated user found.');
        $action->execute();
    }

    public function testExecuteDoesNothingWhenNotImpersonating(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        $manager = app(ImpersonateManager::class);
        $this->assertFalse($manager->isImpersonating());

        $action = app(LeaveImpersonationAction::class);

        // Should not throw exception even when not impersonating
        $this->expectNotToPerformAssertions();
        $action->execute();
    }
}
