<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model::shouldBeStrict(); // Disabled for translations to work
        Model::automaticallyEagerLoadRelationships();

        // Set default pagination view for admin panel
        // This ensures all pagination in admin panel uses the custom view
        LengthAwarePaginator::defaultView('pagination::admin-bootstrap-5');

        $this->configureRateLimiting();
    }

    /**
     * Laravel 11 dropped the RouteServiceProvider that used to define these, and
     * nothing replaced it here - the `api` middleware group contained only
     * SubstituteBindings, so no API route was rate limited at all.
     */
    private function configureRateLimiting(): void
    {
        // General API traffic.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));

        // Credential endpoints. Keyed on IP so an attacker cannot sidestep the
        // limit by rotating the submitted email.
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)
            ->by($request->ip()));

        // Endpoints that send mail to an address supplied in the request.
        // Without a limit these are a spam relay and a cost problem.
        RateLimiter::for('auth-email', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ]);
    }
}
