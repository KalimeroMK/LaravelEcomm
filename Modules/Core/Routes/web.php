<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\SystemController;

// System management routes (admin only) - loaded via RouteServiceProvider with admin prefix
Route::middleware([App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::prefix('system')->name('system.')->group(function () {
        // Index page
        Route::get('/', function () {
            return view('core::system.index');
        })->name('index');

        // Health and version
        Route::get('health', [SystemController::class, 'health'])->name('health');
        Route::get('version', [SystemController::class, 'version'])->name('version');

        // System information
        Route::get('info', [SystemController::class, 'info'])->name('info');

        // Maintenance mode
        Route::post('maintenance/enable', [SystemController::class, 'enableMaintenance'])->name('maintenance.enable');
        Route::post('maintenance/disable', [SystemController::class, 'disableMaintenance'])->name('maintenance.disable');

        // Cache management
        Route::post('cache/clear', [SystemController::class, 'clearCache'])->name('cache.clear');
        Route::post('cache/config/clear', [SystemController::class, 'clearConfig'])->name('cache.config.clear');
        Route::post('cache/route/clear', [SystemController::class, 'clearRoute'])->name('cache.route.clear');
        Route::post('cache/view/clear', [SystemController::class, 'clearView'])->name('cache.view.clear');
        Route::post('cache/all/clear', [SystemController::class, 'clearAll'])->name('cache.all.clear');

        // Database backup
        Route::get('backup', [SystemController::class, 'backupDatabase'])->name('backup');

        // Logs
        Route::get('logs', [SystemController::class, 'logs'])->name('logs');
        Route::get('logs/{filename}', [SystemController::class, 'viewLog'])->name('logs.view');
        Route::post('logs/clear', [SystemController::class, 'clearLogs'])->name('logs.clear');

        // Queue
        Route::get('queue', [SystemController::class, 'queueStatus'])->name('queue');
        Route::get('queue/failed', [SystemController::class, 'failedJobs'])->name('queue.failed');
        Route::post('queue/retry/{id}', [SystemController::class, 'retryJob'])->name('queue.retry');
        Route::post('queue/retry-all', [SystemController::class, 'retryAllJobs'])->name('queue.retry-all');
        Route::delete('queue/failed/{id}', [SystemController::class, 'deleteJob'])->name('queue.delete');

        // Environment
        Route::get('environment', [SystemController::class, 'environment'])->name('environment');
    });
});
