<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Repository\ComplaintRepository;

readonly class UpdateComplaintAction
{
    private ComplaintRepository $repository;

    public function __construct(ComplaintRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ComplaintDTO $dto): Model
    {
        $complaint = $this->repository->findById($dto->id);
        $complaint->update([
            'description' => $dto->description,
            'status' => $dto->status,
        ]);

        return $complaint;
    }
}
