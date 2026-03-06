<?php

declare(strict_types=1);

use App\Http\Middleware\ApiLocaleMiddleware;
use Illuminate\Support\Facades\Route;

// Apply locale middleware to all API routes
Route::middleware([ApiLocaleMiddleware::class])->group(function (): void {
    // Analytics export route with authentication
    Route::middleware('auth:sanctum')->post('/admin/analytics/export', [Modules\Admin\Http\Controllers\Api\AnalyticsController::class, 'export']);

    // Load module API routes (Product, Tag, Shipping, etc.)
    foreach (glob(base_path('Modules/*/Routes/api.php')) as $routeFile) {
        require $routeFile;
    }
});
