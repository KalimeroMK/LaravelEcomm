<?php

declare(strict_types=1);

namespace Modules\Admin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Admin\Http\Controllers';

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
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(module_path('Admin', '/Routes/api.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware(['auth', 'admin', 'web', 'activity'])
            ->prefix('admin')
            ->group(module_path('Admin', '/Routes/web.php'));

        // Analytics routes with proper authentication
        Route::middleware(['auth', 'admin', 'web'])
            ->prefix('admin')
            ->group(function (): void {
                Route::prefix('analytics')->group(function (): void {
                    Route::get('dashboard', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'dashboard']);
                    Route::get('overview', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'overview']);
                    Route::get('sales', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'sales']);
                    Route::get('users', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'users']);
                    Route::get('products', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'products']);
                    Route::get('content', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'content']);
                    Route::get('marketing', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'marketing']);
                    Route::get('performance', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'performance']);
                    Route::get('real-time', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'realTime']);
                    Route::get('date-range', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'dateRange']);
                    Route::post('export', [\Modules\Admin\Http\Controllers\AnalyticsController::class, 'export']);
                });

                // Email Analytics route with proper authentication
                Route::get('email-campaigns/analytics', function (): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory {
                    return view('admin::email-analytics');
                })->name('admin.email-campaigns.analytics');
            });
    }
}
