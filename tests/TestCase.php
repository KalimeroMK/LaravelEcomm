<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Only create roles if they don't exist to avoid memory issues
        if (!Role::where('name', 'super-admin')->exists()) {
            Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'client')->exists()) {
            Role::create(['name' => 'client', 'guard_name' => 'web']);
        }
        
        // Create user only if needed
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
            $user->assignRole('super-admin');
        }
        $this->actingAs($user);
    }
}
