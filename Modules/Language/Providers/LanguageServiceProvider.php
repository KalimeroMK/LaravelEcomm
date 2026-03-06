<?php

declare(strict_types=1);

namespace Modules\Language\Providers;

use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Language', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('Language', 'Config/config.php') => config_path('language.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Language', 'Config/config.php'),
            'language'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/language');

        $sourcePath = module_path('Language', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'language-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'language');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/language');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'language');
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path('Language', 'Resources/lang'), 'language');
            $this->loadJsonTranslationsFrom(module_path('Language', 'Resources/lang'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/language')) {
                $paths[] = $path . '/modules/language';
            }
        }

        return $paths;
    }
}
