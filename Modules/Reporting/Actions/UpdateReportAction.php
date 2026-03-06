<?php

declare(strict_types=1);

namespace Modules\Reporting\Actions;

use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;

readonly class UpdateReportAction
{
    public function execute(Report $report, ReportDTO $dto): Report
    {
        $report->update($dto->toArray());
        return $report->fresh();
    }
}
