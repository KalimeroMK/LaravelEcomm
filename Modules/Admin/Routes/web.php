<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\AnalyticsController;

Route::get('/', [AdminController::class, 'index'])->name('admin');
Route::get('/analytics', function () {
    return view('admin::analytics-dashboard');
})->name('admin.analytics');

Route::get('/email-campaigns/analytics', function () {
    return view('admin::email-analytics');
})->name('admin.email-campaigns.analytics');

// Analytics API Routes for web interface (temporarily without auth for testing)
Route::prefix('analytics')->group(function () {
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

Route::get('/messages/five', [AdminController::class, 'messageFive'])->name('messages.five');
