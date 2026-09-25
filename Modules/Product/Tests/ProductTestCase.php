<?php

declare(strict_types=1);

namespace Modules\Product\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Extends the application TestCase (app bootstrap + TestDataSeeder with the
 * real roles/permissions) instead of hand-rolling createApplication() with a
 * broken bootstrap path and no seeding.
 */
abstract class ProductTestCase extends TestCase
{
    use WithFaker;
}
