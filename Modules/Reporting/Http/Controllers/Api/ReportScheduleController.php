<?php

declare(strict_types=1);

namespace Modules\Reporting\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Reporting\Actions\ScheduleReportAction;
use Modules\Reporting\DTOs\ReportScheduleDTO;
use Modules\Reporting\Http\Requests\ReportScheduleRequest;
use Modules\Reporting\Models\ReportSchedule;

class ReportScheduleController extends CoreController
{
    public function __construct(
        private readonly ScheduleReportAction $scheduleAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schedules = ReportSchedule::query()
            ->with(['report', 'executions'])
            ->whereHas('report', function ($q) use ($request): void {
                $q->forUser($request->user()->id);
            })
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function store(ReportScheduleRequest $request): JsonResponse
    {
        $dto = ReportScheduleDTO::fromRequest([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        $schedule = $this->scheduleAction->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Report scheduled successfully.',
            'data' => $schedule,
        ], 201);
    }

    public function show(ReportSchedule $schedule): JsonResponse
    {
        $schedule->load(['report', 'executions' => function ($query): void {
            $query->latest()->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    public function update(ReportScheduleRequest $request, ReportSchedule $schedule): JsonResponse
    {
        $dto = ReportScheduleDTO::fromRequest([
            ...$request->validated(),
            'created_by' => $schedule->created_by,
        ]);

        $schedule = $this->scheduleAction->update($schedule, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'data' => $schedule,
        ]);
    }

    public function destroy(ReportSchedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.',
        ]);
    }
}
