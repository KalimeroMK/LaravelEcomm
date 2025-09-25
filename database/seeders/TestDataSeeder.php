<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            'view-admin-dashboard',
            'manage-products',
            'manage-orders',
            'manage-users',
            'manage-categories',
            'manage-brands',
            'manage-coupons',
            'manage-bundles',
            'manage-posts',
            'manage-banners',
            'manage-messages',
            'manage-tenants',
            'manage-billing',
            'manage-shipping',
            'manage-settings',
            'view-analytics',
            'manage-newsletter',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin role
        $adminRole->syncPermissions($permissions);

        // Create test users
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $client = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Test Client',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $client->assignRole('client');

        // Basic test data created - roles and users are sufficient for most tests
    }
}
