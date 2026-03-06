<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Reporting\Http\Controllers\Api\ReportController;
use Modules\Reporting\Http\Controllers\Api\ReportExecutionController;
use Modules\Reporting\Http\Controllers\Api\ReportScheduleController;

Route::group([
    'middleware' => ['auth:sanctum', 'role:admin'],
    'prefix' => 'admin',
], function (): void {
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('api.admin.reports.index');
    Route::post('reports', [ReportController::class, 'store'])->name('api.admin.reports.store');
    Route::get('reports/{report}', [ReportController::class, 'show'])->name('api.admin.reports.show');
    Route::put('reports/{report}', [ReportController::class, 'update'])->name('api.admin.reports.update');
    Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('api.admin.reports.destroy');
    Route::post('reports/{report}/generate', [ReportController::class, 'generate'])->name('api.admin.reports.generate');
    Route::post('reports/{report}/export', [ReportController::class, 'export'])->name('api.admin.reports.export');
    
    // Report Schedules
    Route::get('report-schedules', [ReportScheduleController::class, 'index'])->name('api.admin.report-schedules.index');
    Route::post('report-schedules', [ReportScheduleController::class, 'store'])->name('api.admin.report-schedules.store');
    Route::get('report-schedules/{schedule}', [ReportScheduleController::class, 'show'])->name('api.admin.report-schedules.show');
    Route::put('report-schedules/{schedule}', [ReportScheduleController::class, 'update'])->name('api.admin.report-schedules.update');
    Route::delete('report-schedules/{schedule}', [ReportScheduleController::class, 'destroy'])->name('api.admin.report-schedules.destroy');
    
    // Report Executions
    Route::get('report-executions', [ReportExecutionController::class, 'index'])->name('api.admin.report-executions.index');
    Route::get('report-executions/{execution}', [ReportExecutionController::class, 'show'])->name('api.admin.report-executions.show');
    Route::get('report-executions/{execution}/download', [ReportExecutionController::class, 'download'])->name('api.admin.report-executions.download');
    
    // Report Types & Templates
    Route::get('report-types', [ReportController::class, 'types'])->name('api.admin.reports.types');
    Route::get('report-templates', [ReportController::class, 'templates'])->name('api.admin.reports.templates');
});
