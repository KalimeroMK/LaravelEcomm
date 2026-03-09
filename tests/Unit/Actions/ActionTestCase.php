<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Tests\TestCase;

abstract class ActionTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed languages first (required for most operations)
        $this->seed(LanguageDatabaseSeeder::class);
    }
}
