<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Analytics export route with authentication
Route::middleware('auth:sanctum')->post('/admin/analytics/export', [Modules\Admin\Http\Controllers\Api\AnalyticsController::class, 'export']);

// Load module API routes (Product, Tag, Shipping, etc.)
foreach (glob(base_path('Modules/*/Routes/api.php')) as $routeFile) {
    require $routeFile;
}
