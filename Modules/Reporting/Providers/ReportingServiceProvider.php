<?php

declare(strict_types=1);

namespace Modules\Reporting\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Reporting\Console\Commands\RunScheduledReportsCommand;

class ReportingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Reporting', 'Database/Migrations'));
        $this->scheduleCommands();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            RunScheduledReportsCommand::class,
        ]);
    }

    protected function scheduleCommands(): void
    {
        $this->app->booted(function (): void {
            $schedule = $this->app->make(Schedule::class);
            
            // Run scheduled reports every hour
            $schedule->command('reports:run-scheduled')->hourly();
        });
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('Reporting', 'Config/config.php') => config_path('reporting.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Reporting', 'Config/config.php'),
            'reporting'
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/reporting');
        $sourcePath = module_path('Reporting', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'reporting-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'reporting');
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/reporting');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'reporting');
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path('Reporting', 'Resources/lang'), 'reporting');
            $this->loadJsonTranslationsFrom(module_path('Reporting', 'Resources/lang'));
        }
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/reporting')) {
                $paths[] = $path . '/modules/reporting';
            }
        }
        return $paths;
    }
}
