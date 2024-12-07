<?php

namespace Modules\Front\Providers;

use Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Front\Http\ViewComposers\InformationViewComposer;
use Modules\Front\Http\ViewComposers\MaxViewComposer;
use Modules\Front\Http\ViewComposers\MenuViewComposer;
use Modules\Front\Http\ViewComposers\SchemaOrgViewComposer;
use Modules\Front\Http\ViewComposers\SettingsViewComposer;

class FrontServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Front';

    protected string $moduleNameLower = 'front';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $theme = config('theme.active_theme', 'default');
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        View::composer([
            'front::'.$theme.'.layouts.header',
            'front::'.$theme.'.layouts.footer',
            'front::'.$theme.'.pages.about-us'
        ],
            SettingsViewComposer::class);
        View::composer([
            'front::'.$theme.'.pages.product-grids',
            'front::'.$theme.'.layouts.header',
            'front::'.$theme.'.pages.product-lists',
            'front::'.$theme.'.pages.bundles',
        ],
            MenuViewComposer::class);
        View::composer([
            'front::'.$theme.'.pages.product-grids',
            'front::'.$theme.'.pages.product-lists',
            'front::'.$theme.'.pages.bundles'
        ],
            MaxViewComposer::class);
        View::composer('front::'.$theme.'.layouts.master', SchemaOrgViewComposer::class);
        View::composer('front::'.$theme.'.layouts.footer', InformationViewComposer::class);
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
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Gets the publishable view paths for the module.
     *
     * @return array<string> Array of paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
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
}
