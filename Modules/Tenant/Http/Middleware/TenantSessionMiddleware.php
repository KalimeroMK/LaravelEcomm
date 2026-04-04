<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip when multi-tenancy is disabled (normal single-tenant ecom mode)
        if (! config('tenant.multi_tenant.enabled') || ! app()->bound('tenant')) {
            return $next($request);
        }

        $tenant = app('tenant');

        if (! $tenant) {
            return $next($request);
        }

        if (! $request->session()->has('tenant_id')) {
            $request->session()->put('tenant_id', $tenant->id);

            return $next($request);
        }

        if ($request->session()->get('tenant_id') !== $tenant->id) {
            abort(401);
        }

        return $next($request);
    }
}
