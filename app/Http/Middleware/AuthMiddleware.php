<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;

    class AuthMiddleware
    {
        public function handle(Request $request, Closure $next)
        {
            if (auth()->guest()) {
                request()->session()->flash('error', 'Pls login first');
                return redirect()->back();
            }

            return $next($request);
        }
    }