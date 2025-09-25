<?php

declare(strict_types=1);

namespace Modules\Newsletter\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Newsletter\Http\Controllers';

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
        Route::middleware(['auth', 'admin', 'activity', 'web'])
            ->prefix('admin')
            ->group(module_path('Newsletter', '/Routes/web.php'));

        // Admin routes with authentication
        Route::middleware(['auth', 'admin', 'activity', 'web'])
            ->prefix('admin')
            ->group(function (): void {
                // Email Campaigns routes
                Route::get('email-campaigns', [\Modules\Newsletter\Http\Controllers\EmailCampaignController::class, 'index'])->name('admin.email-campaigns.index');
                Route::get('email-campaigns/create', [\Modules\Newsletter\Http\Controllers\EmailCampaignController::class, 'create'])->name('admin.email-campaigns.create');
                Route::post('email-campaigns', [\Modules\Newsletter\Http\Controllers\EmailCampaignController::class, 'store'])->name('admin.email-campaigns.store');
                Route::get('email-campaigns/analytics', [\Modules\Newsletter\Http\Controllers\EmailCampaignController::class, 'analytics'])->name('admin.email-campaigns.analytics');
                Route::get('email-campaigns/analytics/api', [\Modules\Newsletter\Http\Controllers\EmailCampaignController::class, 'analyticsApi'])->name('admin.email-campaigns.analytics.api');

                // Email Templates routes
                Route::get('email-templates', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'index'])->name('admin.email-templates.index');
                Route::get('email-templates/create', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'create'])->name('admin.email-templates.create');
                Route::post('email-templates', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'store'])->name('admin.email-templates.store');
                Route::get('email-templates/{emailTemplate}', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'show'])->name('admin.email-templates.show');
                Route::get('email-templates/{emailTemplate}/edit', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
                Route::put('email-templates/{emailTemplate}', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'update'])->name('admin.email-templates.update');
                Route::delete('email-templates/{emailTemplate}', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'destroy'])->name('admin.email-templates.destroy');
                Route::get('email-templates/{emailTemplate}/preview', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
                Route::post('email-templates/{emailTemplate}/duplicate', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'duplicate'])->name('admin.email-templates.duplicate');
                Route::post('email-templates/{emailTemplate}/set-default', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'setDefault'])->name('admin.email-templates.set-default');
                Route::post('email-templates/{emailTemplate}/toggle-active', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'toggleActive'])->name('admin.email-templates.toggle-active');
                Route::get('email-templates/{emailTemplate}/usage', [\Modules\Newsletter\Http\Controllers\EmailTemplateController::class, 'usage'])->name('admin.email-templates.usage');
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
            ->middleware('auth:sanctum')
            ->group(module_path('Newsletter', '/Routes/api.php'));
    }
}
