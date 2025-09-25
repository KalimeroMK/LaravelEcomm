<?php

declare(strict_types=1);

use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

if (!function_exists('createAdminUser')) {
    function createAdminUser(array $attributes = []): User
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Create user
        $user = User::factory()->create($attributes);
        
        // Assign admin role
        $user->assignRole($adminRole);
        
        return $user;
    }
}

if (!function_exists('createUserWithRole')) {
    function createUserWithRole(string $roleName, array $attributes = []): User
    {
        // Ensure role exists
        $role = Role::firstOrCreate(['name' => $roleName]);
        
        // Create user
        $user = User::factory()->create($attributes);
        
        // Assign role
        $user->assignRole($role);
        
        return $user;
    }
}
