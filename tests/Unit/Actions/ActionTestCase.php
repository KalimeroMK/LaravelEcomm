<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Tests\TestCase;

abstract class ActionTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache so cached queries from prior tests don't bleed through
        Cache::flush();

        // Seed languages first (required for most operations)
        $this->seed(LanguageDatabaseSeeder::class);
    }

    protected function seedPermissions(): void
    {
        $this->seed(PermissionTableSeeder::class);
    }
}
