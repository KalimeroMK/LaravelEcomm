<?php

declare(strict_types=1);

namespace Modules\Reporting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Reporting\Actions\GenerateReportAction;
use Modules\Reporting\Models\ReportSchedule;

class ProcessScheduledReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    public function __construct(
        private readonly ReportSchedule $schedule,
    ) {}

    public function handle(GenerateReportAction $generateAction): void
    {
        if (! $this->schedule->is_active) {
            return;
        }

        $report = $this->schedule->report;
        $dateRange = $this->schedule->getDateRange();
        
        $parameters = array_merge(
            $report->filters ?? [],
            [
                'date_from' => $dateRange['from']->format('Y-m-d'),
                'date_to' => $dateRange['to']->format('Y-m-d'),
            ]
        );

        // Generate and record execution
        $execution = $generateAction->executeAndRecord(
            report: $report,
            scheduleId: $this->schedule->id,
            parameters: $parameters
        );

        // Send email to recipients if successful
        if ($execution->isCompleted() && ! empty($this->schedule->recipients)) {
            // Dispatch email job
            dispatch(new SendReportEmailJob($execution, $this->schedule));
        }

        // Update schedule last/next run
        $this->schedule->markAsRun();
    }

    public function failed(\Throwable $exception): void
    {
        // Log the failure
        logger()->error('Scheduled report failed', [
            'schedule_id' => $this->schedule->id,
            'report_id' => $this->schedule->report_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
