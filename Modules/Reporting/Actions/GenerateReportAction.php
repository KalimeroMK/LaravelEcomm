<?php

declare(strict_types=1);

namespace Modules\Reporting\Actions;

use Illuminate\Support\Collection;
use Modules\Reporting\Models\Report;
use Modules\Reporting\Models\ReportExecution;
use Modules\Reporting\Services\ReportDataService;

readonly class GenerateReportAction
{
    public function __construct(
        private ReportDataService $dataService,
    ) {}

    /**
     * Generate report data
     *
     * @return array{data: Collection, summary: array}
     */
    public function execute(Report $report, array $parameters = []): array
    {
        return $this->dataService->generate($report, $parameters);
    }

    /**
     * Generate and record execution
     */
    public function executeAndRecord(
        Report $report,
        ?int $scheduleId = null,
        ?int $triggeredBy = null,
        array $parameters = []
    ): ReportExecution {
        $execution = ReportExecution::create([
            'report_id' => $report->id,
            'schedule_id' => $scheduleId,
            'status' => ReportExecution::STATUS_RUNNING,
            'trigger_type' => $triggeredBy ? ReportExecution::TRIGGER_MANUAL : ReportExecution::TRIGGER_SCHEDULED,
            'triggered_by' => $triggeredBy,
            'started_at' => now(),
            'parameters' => $parameters,
        ]);

        try {
            $result = $this->execute($report, $parameters);

            $execution->markAsCompleted([
                'record_count' => $result['data']->count(),
                'total_amount' => $result['summary']['total_revenue'] ?? $result['summary']['total'] ?? null,
            ]);

            return $execution;
        } catch (\Exception $e) {
            $execution->markAsFailed($e->getMessage(), $e->getTraceAsString());
            throw $e;
        }
    }
}
