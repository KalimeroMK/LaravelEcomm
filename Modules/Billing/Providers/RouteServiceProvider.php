<?php

declare(strict_types=1);

namespace Modules\Billing\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Billing\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        // Wishlist routes without authentication
        Route::prefix('api/v1')
            ->group(module_path('Billing', '/Routes/api.php'));

        // Other billing routes with authentication
        Route::prefix('api/v1')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::post('stripe', [\Modules\Billing\Http\Controllers\Api\StripeController::class, 'stripe']);
            });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'activity'])
            ->group(module_path('Billing', '/Routes/web.php'));
    }
}
