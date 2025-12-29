<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;

readonly class FindComplaintAction
{
    public function __construct(private ComplaintRepository $repository) {}

    public function execute(int $id): Complaint
    {
        return $this->repository->findById($id);
    }
}
