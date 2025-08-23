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
        Role::findOrCreate('super-admin');
        Role::findOrCreate('client');
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);
    }
}
