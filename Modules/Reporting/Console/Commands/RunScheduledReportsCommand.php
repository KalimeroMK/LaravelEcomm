<?php

declare(strict_types=1);

namespace Modules\Reporting\Console\Commands;

use Illuminate\Console\Command;
use Modules\Reporting\Jobs\ProcessScheduledReportJob;
use Modules\Reporting\Models\ReportSchedule;

class RunScheduledReportsCommand extends Command
{
    protected $signature = 'reports:run-scheduled';

    protected $description = 'Run all scheduled reports that are due';

    public function handle(): int
    {
        $this->info('Checking for scheduled reports...');

        $schedules = ReportSchedule::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('next_run_at')
                      ->orWhere('next_run_at', '<=', now());
            })
            ->with('report')
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No scheduled reports due.');
            return self::SUCCESS;
        }

        $this->info("Found {$schedules->count()} scheduled report(s) to run.");

        foreach ($schedules as $schedule) {
            $this->info("Queueing report: {$schedule->report->name}");
            
            dispatch(new ProcessScheduledReportJob($schedule));
        }

        $this->info('All scheduled reports have been queued.');

        return self::SUCCESS;
    }
}
