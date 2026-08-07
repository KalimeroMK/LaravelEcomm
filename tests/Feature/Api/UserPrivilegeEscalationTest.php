<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression coverage for the privilege-escalation report against
 * PUT /api/v1/users/{id}.
 *
 * The original flaw: UserPolicy::update() permits a user to edit their own
 * profile, and UserController::update() then synced whatever `roles` array the
 * request carried. Any account that could register could therefore promote
 * itself to super-admin.
 */
class UserPrivilegeEscalationTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users';

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'client']);
        Permission::firstOrCreate(['name' => 'user-update']);
        Permission::firstOrCreate(['name' => 'user-list']);
    }

    #[Test]
    public function test_a_client_cannot_promote_itself_to_super_admin(): void
    {
        $attacker = User::factory()->create();
        $attacker->syncRoles(['client']);

        $superAdminRole = Role::findByName('super-admin');
        $token = $attacker->createToken('exploit')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('PUT', $this->url.'/'.$attacker->id, [
                'name' => 'Still Just A Client',
                'roles' => [$superAdminRole->id],
            ]);

        $this->assertContains(
            $response->getStatusCode(),
            [403, 422],
            'Self-assigning a role must be rejected.'
        );

        $attacker->refresh();
        $this->assertFalse(
            $attacker->hasRole('super-admin'),
            'Privilege escalation: the attacker obtained the super-admin role.'
        );
    }

    #[Test]
    public function test_a_client_cannot_promote_another_user(): void
    {
        $attacker = User::factory()->create();
        $attacker->syncRoles(['client']);

        $accomplice = User::factory()->create();
        $accomplice->syncRoles(['client']);

        $superAdminRole = Role::findByName('super-admin');
        $token = $attacker->createToken('exploit')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('PUT', $this->url.'/'.$accomplice->id, [
                'roles' => [$superAdminRole->id],
            ]);

        $this->assertContains($response->getStatusCode(), [403, 422]);

        $accomplice->refresh();
        $this->assertFalse($accomplice->hasRole('super-admin'));
    }

    #[Test]
    public function test_a_user_with_user_update_still_cannot_grant_super_admin(): void
    {
        // An operator who legitimately manages users must still not be able to
        // mint a super-admin - that is reserved for existing super-admins.
        $operator = User::factory()->create();
        $operator->givePermissionTo('user-update');

        $target = User::factory()->create();
        $superAdminRole = Role::findByName('super-admin');
        $token = $operator->createToken('operator')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('PUT', $this->url.'/'.$target->id, [
                'roles' => [$superAdminRole->id],
            ]);

        $response->assertStatus(422);

        $target->refresh();
        $this->assertFalse($target->hasRole('super-admin'));
    }

    #[Test]
    public function test_a_client_may_still_edit_its_own_profile(): void
    {
        // The fix must not break legitimate self-service profile edits.
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->syncRoles(['client']);

        $token = $user->createToken('self')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('PUT', $this->url.'/'.$user->id, [
                'name' => 'New Name',
            ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertSame('New Name', $user->name);
    }

    #[Test]
    public function test_a_super_admin_may_still_assign_roles(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['super-admin']);

        $target = User::factory()->create();
        $clientRole = Role::findByName('client');
        $token = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('PUT', $this->url.'/'.$target->id, [
                'roles' => [$clientRole->id],
            ]);

        $response->assertStatus(200);

        $target->refresh();
        $this->assertTrue($target->hasRole('client'));
    }
}
