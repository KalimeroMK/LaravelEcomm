<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelLogger\App\Models\Activity as BaseActivity;
use Modules\Core\Models\Activity;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind our custom Activity model to override the package's model
        $this->app->bind(BaseActivity::class, Activity::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
