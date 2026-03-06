<?php

declare(strict_types=1);

namespace Modules\Reporting\Actions;

use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;

readonly class CreateReportAction
{
    public function execute(ReportDTO $dto): Report
    {
        return Report::create($dto->toArray());
    }
}
