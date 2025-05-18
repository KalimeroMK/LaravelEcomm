<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure the 'client' role exists for the web guard
        if (class_exists(Role::class)) {
            Role::firstOrCreate([
                'name' => 'client',
                'guard_name' => 'web',
            ]);
        }
    }
}
