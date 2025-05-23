<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Modules\Complaint\Models\Complaint;

readonly class DeleteComplaintAction
{
    public function execute(int $id): bool
    {
        $complaint = Complaint::findOrFail($id);

        return $complaint->delete();
    }
}
