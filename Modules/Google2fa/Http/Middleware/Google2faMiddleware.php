<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Google2fa\Support\Google2FAAuthenticator;

class Google2faMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $authenticator = app(Google2FAAuthenticator::class)->boot($request);

        return $authenticator->isAuthenticated()
            ? $next($request)
            : $authenticator->makeRequestOneTimePasswordResponse();
    }
}
