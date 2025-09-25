<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\TestDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations and seeders
        $this->artisan('migrate:fresh');
        $this->seed(TestDataSeeder::class);
    }
}
