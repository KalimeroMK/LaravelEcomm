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

        // Create roles and assign ALL permissions to each
        $roles = ['manager', 'client', 'admin'];
        
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            // Give ALL permissions to this role
            $role->syncPermissions($allPermissions);
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
