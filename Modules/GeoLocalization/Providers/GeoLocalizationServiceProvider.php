<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\Providers;

use Illuminate\Support\ServiceProvider;

class GeoLocalizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerMiddleware();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('GeoLocalization', 'Config/config.php') => config_path('geolocalization.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('GeoLocalization', 'Config/config.php'),
            'geolocalization'
        );
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('geolocalization', \Modules\GeoLocalization\Http\Middleware\GeoIpMiddleware::class);
        
        // Add to web middleware group if enabled
        if (config('geolocalization.enabled', true)) {
            $router->pushMiddlewareToGroup('web', \Modules\GeoLocalization\Http\Middleware\GeoIpMiddleware::class);
            $router->pushMiddlewareToGroup('api', \Modules\GeoLocalization\Http\Middleware\GeoIpMiddleware::class);
        }
    }
}
