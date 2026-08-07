<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\ActivityLoggerServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Applies `throttle:api` to the api group. Laravel 11 stopped doing this
        // by default, so every API route was unlimited.
        $middleware->throttleApi();

        // spatie/laravel-permission v6+ no longer registers these aliases itself.
        // Without them any route using `role:`/`permission:` fails with
        // "Target class [role] does not exist" instead of authorizing.
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
