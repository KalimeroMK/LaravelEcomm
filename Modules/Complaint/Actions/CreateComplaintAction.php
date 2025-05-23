<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Models\Complaint;

readonly class CreateComplaintAction
{
    public function execute(ComplaintDTO $dto): Complaint
    {
        return Complaint::create([
            'user_id' => $dto->user_id,
            'order_id' => $dto->order_id,
            'description' => $dto->description,
            'status' => $dto->status,
        ]);
    }
}
