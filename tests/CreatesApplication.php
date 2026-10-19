<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $this->assertConfigIsNotCached();

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Refuse to run against a cached configuration.
     *
     * A warm bootstrap/cache/config.php wins over every <env> in phpunit.xml,
     * so the suite silently picks up the real DB_CONNECTION, CACHE_STORE and
     * SESSION_DRIVER: tests then run against the production MySQL database
     * instead of sqlite, and CSRF is enforced because app.env reads
     * 'production'. Fail loudly instead of producing meaningless results.
     */
    private function assertConfigIsNotCached(): void
    {
        if (file_exists(__DIR__.'/../bootstrap/cache/config.php')) {
            throw new RuntimeException(
                'The configuration is cached, so phpunit.xml cannot override it and the '
                .'suite would run against the real database. Run `php artisan config:clear` first.'
            );
        }
    }
}
