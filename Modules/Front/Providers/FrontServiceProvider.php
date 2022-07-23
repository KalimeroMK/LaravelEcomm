<?php

namespace Modules\Front\Providers;

use Config;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Front\Http\ViewComposers\MenuViewComposer;
use Modules\Front\Http\ViewComposers\SchemaOrgViewComposer;
use Modules\Front\Http\ViewComposers\SettingsViewComposer;

class FrontServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Front';
    
    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'front';
    
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        
        View::composer(['front::layouts.header', 'front::layouts.footer', 'front::pages.about-us'], SettingsViewComposer::class);
        View::composer(['front::pages.product-grids', 'front::layouts.header', 'front::pages.product-lists'], MenuViewComposer::class);
        View::composer('front::layouts.master', SchemaOrgViewComposer::class);
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
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
     *
     * @return void
     */
    protected function registerConfig()
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
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        
        $sourcePath = module_path($this->moduleName, 'Resources/views');
        
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);
        
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }
    
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
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
