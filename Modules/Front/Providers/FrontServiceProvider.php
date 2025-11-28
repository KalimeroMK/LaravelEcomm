<?php

declare(strict_types=1);

namespace Modules\Front\Providers;

use Config;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Front\Http\ViewComposers\InformationViewComposer;
use Modules\Front\Http\ViewComposers\MaxViewComposer;
use Modules\Front\Http\ViewComposers\MenuViewComposer;
use Modules\Front\Http\ViewComposers\SchemaOrgViewComposer;
use Modules\Front\Http\ViewComposers\SettingsViewComposer;
use Modules\Front\Http\ViewComposers\ThemeViewComposer;

class FrontServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Front';

    protected string $moduleNameLower = 'front';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Ensure helpers are loaded before boot
        $this->loadThemeHelpers();

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Register SettingsViewComposer for all theme views
        View::composer(
            [
                'front::layouts.header',
                'front::layouts.footer',
                'front::pages.about-us',
                'front::themes.*.layouts.header',
                'front::themes.*.layouts.footer',
                'front::themes.*.pages.about-us',
            ],
            SettingsViewComposer::class
        );
        View::composer(
            [
                'front::pages.product-grids',
                'front::layouts.header',
                'front::pages.product-lists',
                'front::pages.bundles',
                'front::themes.*.layouts.header',
                'front::themes.*.pages.product-grids',
                'front::themes.*.pages.product-lists',
                'front::themes.*.pages.bundles',
            ],
            MenuViewComposer::class
        );
        View::composer(
            [
                'front::pages.product-grids',
                'front::pages.product-lists',
                'front::pages.bundles',
                'front::pages.index',
                'front::themes.*.pages.product-grids',
                'front::themes.*.pages.product-lists',
                'front::themes.*.pages.bundles',
                'front::themes.*.pages.index',
            ],
            MaxViewComposer::class
        );
        View::composer('front::layouts.master', SchemaOrgViewComposer::class);
        View::composer(
            [
                'front::layouts.footer',
                'front::themes.*.layouts.footer',
            ],
            InformationViewComposer::class
        );
        // Register ThemeViewComposer for all views to ensure themePath and activeTheme are always available
        View::composer('*', ThemeViewComposer::class);

        // Also ensure SettingsViewComposer runs for all front views
        View::composer('front::*', SettingsViewComposer::class);
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);

        // Register theme-specific views with fallback to default theme
        $activeTheme = $this->getActiveTheme();
        $themePath = $sourcePath.'/themes/'.$activeTheme;
        $defaultThemePath = $sourcePath.'/themes/default';

        // Build view paths array
        $viewPaths = $this->getPublishableViewPaths();

        // Register themes directory structure for proper namespace resolution
        // This allows views to be found as front::themes.default.index
        $themesPath = $sourcePath.'/themes';
        if (is_dir($themesPath)) {
            // Register each theme directory individually
            $themeDirs = scandir($themesPath);
            foreach ($themeDirs as $themeDir) {
                if ($themeDir !== '.' && $themeDir !== '..' && is_dir($themesPath.'/'.$themeDir)) {
                    $viewPaths[] = $themesPath.'/'.$themeDir;
                }
            }
        }

        // Also register base source path for legacy views
        $viewPaths[] = $sourcePath;

        $this->loadViewsFrom($viewPaths, $this->moduleNameLower);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        // Load theme helpers early to ensure they're available globally
        $this->loadThemeHelpers();
    }

    /**
     * Gets the publishable view paths for the module.
     *
     * @return array<string> Array of paths.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Get the active theme from settings.
     */
    private function getActiveTheme(): string
    {
        try {
            $setting = \Modules\Settings\Models\Setting::first();

            return $setting->active_template ?? 'default';
        } catch (Exception $e) {
            return 'default';
        }
    }

    /**
     * Load theme helper functions.
     */
    private function loadThemeHelpers(): void
    {
        $helperPath = base_path('Modules/Front/Helpers/theme.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Gets the publishable view paths for the module.
     *
     * @return array<string> Array of paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
