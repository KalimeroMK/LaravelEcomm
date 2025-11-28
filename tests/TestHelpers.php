<?php

declare(strict_types=1);

use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

if (! function_exists('createAdminUser')) {
    function createAdminUser(array $attributes = []): User
    {
        // Ensure admin role exists with web guard
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['guard_name' => 'web']
        );

        // Generate unique email if not provided
        if (! isset($attributes['email'])) {
            $attributes['email'] = 'admin'.uniqid().'@test.com';
        }

        // Create user
        $user = User::factory()->create($attributes);

        // Assign admin role
        $user->assignRole($adminRole);

        return $user;
    }
}

if (! function_exists('createSuperAdminUser')) {
    function createSuperAdminUser(array $attributes = []): User
    {
        // Ensure super-admin role exists with web guard
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['guard_name' => 'web']
        );

        // Generate unique email if not provided
        if (! isset($attributes['email'])) {
            $attributes['email'] = 'superadmin'.uniqid().'@test.com';
        }

        // Create user
        $user = User::factory()->create($attributes);

        // Assign super-admin role
        $user->assignRole($superAdminRole);

        return $user;
    }
}

if (! function_exists('createUserWithRole')) {
    function createUserWithRole(string $roleName, array $attributes = []): User
    {
        // Ensure role exists with web guard
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            ['guard_name' => 'web']
        );

        // Create user
        $user = User::factory()->create($attributes);

        // Assign role
        $user->assignRole($role);

        return $user;
    }
}
