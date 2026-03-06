<?php

declare(strict_types=1);

namespace Modules\Product\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Product\Providers\ProductServiceProvider;

abstract class ProductTestCase extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load product module service provider
        $this->app->register(ProductServiceProvider::class);
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
