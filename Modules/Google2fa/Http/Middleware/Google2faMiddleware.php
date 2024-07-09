<?php

namespace Modules\Google2fa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Google2fa\Support\Google2FAAuthenticator;

class Google2faMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authenticator = app(Google2FAAuthenticator::class)->boot($request);

        return $authenticator->isAuthenticated() ? $next($request) : $authenticator->makeRequestOneTimePasswordResponse(
        );
    }
}