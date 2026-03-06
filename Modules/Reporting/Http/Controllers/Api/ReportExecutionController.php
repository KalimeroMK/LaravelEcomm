<?php

declare(strict_types=1);

namespace Modules\Reporting\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Reporting\Models\ReportExecution;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExecutionController extends CoreController
{
    public function index(Request $request): JsonResponse
    {
        $executions = ReportExecution::query()
            ->with(['report', 'triggeredBy'])
            ->whereHas('report', function ($q) use ($request): void {
                $q->forUser($request->user()->id);
            })
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $executions,
        ]);
    }

    public function show(ReportExecution $execution): JsonResponse
    {
        $execution->load(['report', 'schedule', 'triggeredBy']);

        // Load the actual report data for preview
        $data = [];
        if ($execution->isCompleted() && $execution->parameters) {
            $report = $execution->report;
            $service = app(\Modules\Reporting\Services\ReportDataService::class);
            $result = $service->generate($report, $execution->parameters);
            $data = $result['data'];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'execution' => $execution,
                'results' => $data,
            ],
        ]);
    }

    public function download(ReportExecution $execution): StreamedResponse|JsonResponse
    {
        if (! $execution->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Report execution is not completed yet.',
            ], 400);
        }

        if ($execution->file_path && \Storage::exists($execution->file_path)) {
            return \Storage::download($execution->file_path);
        }

        // Regenerate if file is missing
        $report = $execution->report;
        $service = app(\Modules\Reporting\Services\ReportDataService::class);
        $result = $service->generate($report, $execution->parameters ?? []);

        $exportAction = app(\Modules\Reporting\Actions\ExportReportAction::class);
        
        return $exportAction->execute($report, $report->format, $execution->parameters ?? []);
    }
}
