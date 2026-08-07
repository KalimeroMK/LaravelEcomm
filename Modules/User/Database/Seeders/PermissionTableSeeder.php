<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @throws Exception
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define resources and their respective CRUD operations
        $resources = [
            'attribute',
            'attribute-group',
            'banner',
            'brand',
            'bundle',
            'cart',
            'category',
            'complaint',
            'comment',
            'coupon',
            'casys',
            'message',
            'newsletter',
            'notification',
            'order',
            'page',
            'permission',
            'post',
            'product',
            'payment-provider',
            'role',
            'review',
            'settings',
            'shipping',
            'tag',
            'user',
            'product-stats',
        ];

        $operations = ['list', 'show', 'create', 'update', 'delete'];

        // Create permissions
        foreach ($resources as $resource) {
            foreach ($operations as $operation) {
                Permission::firstOrCreate(['name' => "{$resource}-{$operation}"]);
            }
        }

        // Get all permissions
        $allPermissions = Permission::all();

        // Roles are seeded least-privilege. `client` is the role every new
        // registration receives (see UserObserver), so it must never carry
        // administrative permissions - otherwise signing up grants control of
        // the application.
        foreach ($this->rolePermissions($resources, $operations) as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        // Super-admin gets all permissions (and Gate::before allows everything anyway)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions($allPermissions);

        // Create demo users and assign roles
        $this->createUserWithRole('Example User', 'manager@mail.com', 'manager');
        $this->createUserWithRole('Example Client User', 'client@mail.com', 'client');
        $this->createUserWithRole('Example Admin User', 'admin@mail.com', 'admin');
        $this->createUserWithRole('Example Super-Admin User', 'superadmin@mail.com', 'super-admin');
    }

    /**
     * Least-privilege permission matrix, keyed by role name.
     *
     * Built from the generated permission set so it cannot drift out of sync
     * when a resource is added above.
     *
     * @param  list<string>  $resources
     * @param  list<string>  $operations
     * @return array<string, list<string>>
     */
    private function rolePermissions(array $resources, array $operations): array
    {
        // Identity and system configuration are off-limits to managers.
        $managerDenied = ['user', 'role', 'permission', 'settings', 'casys', 'payment-provider', 'cart'];

        // Permission CRUD stays with super-admin only.
        $adminDenied = ['permission'];

        return [
            // Assigned automatically to every new registration (UserObserver).
            // Ownership is enforced separately by the Order/Cart/Complaint
            // policies, so these permissions gate the endpoint, not the row.
            'client' => [
                ...$this->permissionsFor(['cart'], $operations),
                ...$this->permissionsFor(
                    ['order', 'complaint', 'review', 'comment'],
                    ['list', 'show', 'create']
                ),
                ...$this->permissionsFor(
                    ['product', 'category', 'brand', 'post', 'page', 'tag'],
                    ['list', 'show']
                ),
            ],

            // Operational staff: catalog, content and orders - no identity or config.
            'manager' => [
                ...$this->permissionsFor(
                    array_values(array_diff($resources, $managerDenied)),
                    $operations
                ),
                ...$this->permissionsFor(['cart'], ['list', 'show']),
            ],

            'admin' => $this->permissionsFor(
                array_values(array_diff($resources, $adminDenied)),
                $operations
            ),
        ];
    }

    /**
     * Expand a resource/operation matrix into permission names.
     *
     * @param  list<string>  $resources
     * @param  list<string>  $operations
     * @return list<string>
     */
    private function permissionsFor(array $resources, array $operations): array
    {
        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($operations as $operation) {
                $permissions[] = "{$resource}-{$operation}";
            }
        }

        return $permissions;
    }

    /**
     * @throws Exception
     */
    private function createUserWithRole(string $name, string $email, string $roleName): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
            ]
        );

        if (! $user instanceof User) {
            throw new Exception('User creation did not return a User model instance.');
        }

        $user->syncRoles([$roleName]);
    }
}
