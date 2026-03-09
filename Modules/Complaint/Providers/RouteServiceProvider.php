<?php

declare(strict_types=1);

namespace Modules\Complaint\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Complaint\Http\Controllers';

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
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        // Admin routes
        Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->group(module_path('Complaint', '/Routes/web.php'));

        // Client-facing routes (no admin middleware)
        Route::middleware(['web', 'auth'])
            ->group(function (): void {
                Route::get('my-complaints', [\Modules\Complaint\Http\Controllers\UserComplaintController::class, 'index'])->name('user.complaints.index');
                Route::get('my-complaints/{complaint}', [\Modules\Complaint\Http\Controllers\UserComplaintController::class, 'show'])->name('user.complaints.show');
                Route::get('orders/{order}/complaint/create', [\Modules\Complaint\Http\Controllers\UserComplaintController::class, 'create'])->name('user.complaints.create');
                Route::post('orders/{order}/complaint', [\Modules\Complaint\Http\Controllers\UserComplaintController::class, 'store'])->name('user.complaints.store');
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/v1')
            ->middleware(['api', 'auth:sanctum'])
            ->namespace($this->moduleNamespace)
            ->group(module_path('Complaint', '/Routes/api.php'));
    }
}
