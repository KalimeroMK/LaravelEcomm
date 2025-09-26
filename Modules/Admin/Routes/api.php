<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\Api\AnalyticsController;
use Modules\Admin\Http\Controllers\Api\UserBehaviorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Routes
Route::middleware('auth:api')->get('/admin', function (Request $request) {
    return $request->user();
});

// Analytics API Routes
Route::prefix('admin/analytics')->middleware(['auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->group(function (): void {
    Route::get('dashboard', [AnalyticsController::class, 'dashboard'])->name('admin.analytics.dashboard');
    Route::get('overview', [AnalyticsController::class, 'overview'])->name('admin.analytics.overview');
    Route::get('sales', [AnalyticsController::class, 'sales'])->name('admin.analytics.sales');
    Route::get('users', [AnalyticsController::class, 'users'])->name('admin.analytics.users');
    Route::get('products', [AnalyticsController::class, 'products'])->name('admin.analytics.products');
    Route::get('content', [AnalyticsController::class, 'content'])->name('admin.analytics.content');
    Route::get('marketing', [AnalyticsController::class, 'marketing'])->name('admin.analytics.marketing');
    Route::get('performance', [AnalyticsController::class, 'performance'])->name('admin.analytics.performance');
    Route::get('real-time', [AnalyticsController::class, 'realTime'])->name('admin.analytics.real-time');
    Route::get('date-range', [AnalyticsController::class, 'dateRange'])->name('admin.analytics.date-range');
    Route::post('export', [AnalyticsController::class, 'export'])->name('admin.analytics.export');
});

// User Behavior Tracking Routes
Route::prefix('admin/analytics')->group(function (): void {
    Route::post('track', [UserBehaviorController::class, 'track'])->name('admin.analytics.track');
    Route::get('behavior', [UserBehaviorController::class, 'analytics'])->name('admin.analytics.behavior');
    Route::get('page-views', [UserBehaviorController::class, 'pageViews'])->name('admin.analytics.page-views');
    Route::get('engagement', [UserBehaviorController::class, 'engagement'])->name('admin.analytics.engagement');
    Route::get('popular-pages', [UserBehaviorController::class, 'popularPages'])->name('admin.analytics.popular-pages');
    Route::get('sessions', [UserBehaviorController::class, 'sessions'])->name('admin.analytics.sessions');
    Route::get('devices', [UserBehaviorController::class, 'devices'])->name('admin.analytics.devices');
    Route::get('geographic', [UserBehaviorController::class, 'geographic'])->name('admin.analytics.geographic');
});
