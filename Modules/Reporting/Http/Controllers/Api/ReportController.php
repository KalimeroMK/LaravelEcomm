<?php

declare(strict_types=1);

namespace Modules\Reporting\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\Actions\ExportReportAction;
use Modules\Reporting\Actions\GenerateReportAction;
use Modules\Reporting\Actions\UpdateReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Http\Requests\ReportRequest;
use Modules\Reporting\Models\Report;

class ReportController extends CoreController
{
    public function __construct(
        private readonly CreateReportAction $createAction,
        private readonly UpdateReportAction $updateAction,
        private readonly GenerateReportAction $generateAction,
        private readonly ExportReportAction $exportAction,
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Report::query()
            ->forUser($request->user()->id)
            ->with(['lastExecution']);

        if ($request->has('type')) {
            $query->byType($request->get('type'));
        }

        if ($request->has('template')) {
            $query->templates();
        }

        $reports = $query->ordered()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function store(ReportRequest $request): JsonResponse
    {
        $dto = ReportDTO::fromRequest([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        $report = $this->createAction->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Report created successfully.',
            'data' => $report,
        ], 201);
    }

    public function show(Report $report): JsonResponse
    {
        $report->load(['executions' => function ($query): void {
            $query->latest()->limit(10);
        }, 'schedules']);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function update(ReportRequest $request, Report $report): JsonResponse
    {
        $dto = ReportDTO::fromRequest([
            ...$request->validated(),
            'created_by' => $report->created_by,
        ]);

        $report = $this->updateAction->execute($report, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Report updated successfully.',
            'data' => $report,
        ]);
    }

    public function destroy(Report $report): JsonResponse
    {
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully.',
        ]);
    }

    public function generate(Request $request, Report $report): JsonResponse
    {
        $parameters = $request->get('parameters', []);
        
        $result = $this->generateAction->executeAndRecord(
            report: $report,
            triggeredBy: $request->user()->id,
            parameters: $parameters
        );

        return response()->json([
            'success' => true,
            'data' => [
                'execution' => $result,
                'preview_url' => route('api.admin.report-executions.show', $result),
            ],
        ]);
    }

    public function export(Request $request, Report $report): mixed
    {
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'parameters' => 'nullable|array',
        ]);

        $format = $request->get('format');
        $parameters = $request->get('parameters', []);

        return $this->exportAction->execute($report, $format, $parameters);
    }

    public function types(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'types' => Report::TYPES,
                'formats' => Report::FORMATS,
            ],
        ]);
    }

    public function templates(Request $request): JsonResponse
    {
        $templates = Report::templates()
            ->public()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}
