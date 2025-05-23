<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Models\Complaint;

readonly class UpdateComplaintAction
{
    public function execute(ComplaintDTO $dto): Complaint
    {
        $complaint = Complaint::findOrFail($dto->id);
        $complaint->update([
            'description' => $dto->description,
            'status' => $dto->status,
        ]);

        return $complaint;
    }
}
