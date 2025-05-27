<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Modules\Complaint\Repository\ComplaintRepository;

readonly class DeleteComplaintAction
{
    public function __construct(private ComplaintRepository $repository)
    {
    }

    public function execute(int $id): bool
    {
        $this->repository->destroy($id);
    }
}
