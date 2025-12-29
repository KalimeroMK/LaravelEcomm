<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Order\Http\Controllers';

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
        Route::prefix('api/v1')
            ->middleware('auth:sanctum')
            ->group(module_path('Order', '/Routes/api.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        // Admin routes
        Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class, 'web', 'activity'])
            ->prefix('admin')
            ->group(module_path('Order', '/Routes/web.php'));

        // User-facing routes (no admin prefix)
        Route::middleware(['auth', 'web'])
            ->group(function (): void {
                Route::get('my-orders', [\Modules\Order\Http\Controllers\UserOrderController::class, 'history'])->name('user.orders.history');
                Route::get('my-orders/{order}', [\Modules\Order\Http\Controllers\UserOrderController::class, 'detail'])->name('user.orders.detail');
                Route::get('my-orders/{order}/track', [\Modules\Order\Http\Controllers\UserOrderController::class, 'track'])->name('user.orders.track');
            });
    }
}
