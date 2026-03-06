<?php

declare(strict_types=1);

namespace Modules\Reporting\Actions;

use Modules\Reporting\DTOs\ReportScheduleDTO;
use Modules\Reporting\Models\ReportSchedule;

readonly class ScheduleReportAction
{
    public function execute(ReportScheduleDTO $dto): ReportSchedule
    {
        $data = $dto->toArray();
        
        // Calculate next run time
        $schedule = new ReportSchedule($data);
        $data['next_run_at'] = $schedule->calculateNextRun();
        
        return ReportSchedule::create($data);
    }

    public function update(ReportSchedule $schedule, ReportScheduleDTO $dto): ReportSchedule
    {
        $data = $dto->toArray();
        
        // Recalculate next run time if schedule changed
        $tempSchedule = new ReportSchedule($data);
        $data['next_run_at'] = $tempSchedule->calculateNextRun();
        
        $schedule->update($data);
        return $schedule->fresh();
    }
}
