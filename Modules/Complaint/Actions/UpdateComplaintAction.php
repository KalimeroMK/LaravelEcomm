<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Repository\ComplaintRepository;

readonly class UpdateComplaintAction
{
    public function __construct(private ComplaintRepository $repository)
    {
    }

    public function execute(ComplaintDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'description' => $dto->description,
            'status' => $dto->status,
        ]);
    }
}
