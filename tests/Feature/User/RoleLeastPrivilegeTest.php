<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The permission seeder previously did this:
 *
 *     $roles = ['manager', 'client', 'admin'];
 *     foreach ($roles as $roleName) {
 *         $role->syncPermissions($allPermissions);   // ALL permissions
 *     }
 *
 * Because UserObserver assigns `client` to every new registration, that handed
 * full administrative permissions to anyone who signed up - a wider hole than
 * the reported role-escalation path, and reachable without any exploit at all.
 */
class RoleLeastPrivilegeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionTableSeeder::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function administrativePermissions(): array
    {
        return [
            'edit users' => ['user-update'],
            'delete users' => ['user-delete'],
            'create users' => ['user-create'],
            'create roles' => ['role-create'],
            'delete roles' => ['role-delete'],
            'manage permissions' => ['permission-create'],
            'change settings' => ['settings-update'],
            'delete products' => ['product-delete'],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('administrativePermissions')]
    public function test_the_client_role_holds_no_administrative_permission(string $permission): void
    {
        $client = Role::findByName('client');

        $this->assertFalse(
            $client->hasPermissionTo($permission),
            sprintf('The default customer role must not hold "%s".', $permission)
        );
    }

    #[Test]
    public function test_a_freshly_registered_user_cannot_administer_the_application(): void
    {
        // UserObserver assigns the `client` role on creation.
        $newcomer = User::factory()->create();

        $this->assertTrue($newcomer->hasRole('client'), 'Sanity check: registration assigns the client role.');

        foreach (array_merge(...array_values(self::administrativePermissions())) as $permission) {
            $this->assertFalse(
                $newcomer->can($permission),
                sprintf('A newly registered account must not be able to "%s".', $permission)
            );
        }
    }

    #[Test]
    public function test_the_client_role_keeps_the_permissions_a_customer_needs(): void
    {
        $client = Role::findByName('client');

        foreach (['cart-create', 'order-create', 'order-list', 'complaint-create', 'product-list'] as $permission) {
            $this->assertTrue(
                $client->hasPermissionTo($permission),
                sprintf('Customers still need "%s".', $permission)
            );
        }
    }

    #[Test]
    public function test_the_manager_role_cannot_touch_identity_or_configuration(): void
    {
        $manager = Role::findByName('manager');

        foreach (['user-update', 'user-delete', 'role-create', 'permission-create', 'settings-update'] as $permission) {
            $this->assertFalse(
                $manager->hasPermissionTo($permission),
                sprintf('Managers must not hold "%s".', $permission)
            );
        }

        // ...but must still run the catalogue.
        $this->assertTrue($manager->hasPermissionTo('product-update'));
        $this->assertTrue($manager->hasPermissionTo('order-update'));
    }

    #[Test]
    public function test_super_admin_retains_full_control(): void
    {
        $superAdmin = Role::findByName('super-admin');

        foreach (['user-update', 'role-create', 'permission-create', 'settings-update'] as $permission) {
            $this->assertTrue($superAdmin->hasPermissionTo($permission));
        }
    }
}
