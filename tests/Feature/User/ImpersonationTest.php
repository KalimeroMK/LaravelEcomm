<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * lab404/laravel-impersonate ships canImpersonate() and canBeImpersonated()
 * defaulting to true. Neither was overridden, /admin/{user}/impersonate carried
 * only the `auth` middleware, and UserController::impersonate() is not a
 * resource method so authorizeResource() never applied to it.
 *
 * A plain customer could therefore take over any account - including a
 * super-admin - with a single GET request, no role manipulation involved.
 */
class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionTableSeeder::class);
    }

    #[Test]
    public function test_a_customer_cannot_impersonate_a_super_admin(): void
    {
        $attacker = User::factory()->create();
        $attacker->syncRoles(['client']);

        $victim = User::factory()->create();
        $victim->syncRoles(['super-admin']);

        $this->actingAs($attacker)->get("/admin/{$victim->id}/impersonate");

        $this->assertSame(
            $attacker->id,
            auth()->id(),
            'Account takeover: the customer is now authenticated as another user.'
        );
        $this->assertFalse(auth()->user()->hasRole('super-admin'));
    }

    #[Test]
    public function test_a_customer_cannot_impersonate_another_customer(): void
    {
        $attacker = User::factory()->create();
        $attacker->syncRoles(['client']);

        $victim = User::factory()->create();
        $victim->syncRoles(['client']);

        $this->actingAs($attacker)->get("/admin/{$victim->id}/impersonate");

        $this->assertSame($attacker->id, auth()->id());
    }

    #[Test]
    public function test_an_admin_cannot_impersonate_a_super_admin(): void
    {
        // Otherwise impersonation is itself a privilege-escalation path.
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $victim = User::factory()->create();
        $victim->syncRoles(['super-admin']);

        $this->actingAs($admin)->get("/admin/{$victim->id}/impersonate");

        $this->assertSame($admin->id, auth()->id());
        $this->assertFalse(auth()->user()->hasRole('super-admin'));
    }

    #[Test]
    public function test_an_admin_can_still_impersonate_a_customer(): void
    {
        // Support staff need this - the fix must not remove the feature.
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $customer = User::factory()->create();
        $customer->syncRoles(['client']);

        $this->actingAs($admin)->get("/admin/{$customer->id}/impersonate");

        $this->assertSame(
            $customer->id,
            auth()->id(),
            'Legitimate impersonation by an admin should still work.'
        );
    }
}
