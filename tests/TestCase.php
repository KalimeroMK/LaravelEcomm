<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\TestDataSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Load theme helpers
        $helperPath = base_path('Modules/Front/Helpers/theme.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }

        // RefreshDatabase trait automatically runs migrations
        // Seed test data - most tests need it
        try {
            $this->seed(TestDataSeeder::class);
        } catch (Exception $e) {
            // If seeding fails, continue - some tests may not need it
        }
    }
}
