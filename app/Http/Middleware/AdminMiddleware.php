<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow all authenticated users since they all have permissions via roles
        // If you want to restrict by specific roles, uncomment the line below:
        // if (! $user->hasAnyRole(['admin', 'super-admin', 'manager', 'client'])) {
        //     abort(403, 'Unauthorized - Admin access required');
        // }

        return $next($request);
    }
}
