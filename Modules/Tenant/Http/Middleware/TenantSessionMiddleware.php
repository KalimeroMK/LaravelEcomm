<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('tenant_id')) {
            $request->session()->put('tenant_id', app('tenant')->id);

            return $next($request);
        }
        if ($request->session()->get('tenant_id') != app('tenant')->id) {
            abort(401);
        }

        return $next($request);
    }
}
