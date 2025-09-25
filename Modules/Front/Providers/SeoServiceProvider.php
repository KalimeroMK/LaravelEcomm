<?php

declare(strict_types=1);

namespace Modules\Front\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Front\Http\ViewComposers\SeoViewComposer;
use Modules\Front\Services\SeoService;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SeoService::class, function ($app): SeoService {
            return new SeoService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register view composers
        View::composer([
            'front::layouts.seo-master',
            'front::pages.seo-product-detail',
            'front::pages.product_detail',
            'front::pages.bundle_detail',
        ], SeoViewComposer::class);

        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/seo.php' => config_path('seo.php'),
        ], 'seo-config');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\GenerateSeoSitemap::class,
            ]);
        }
    }
}
