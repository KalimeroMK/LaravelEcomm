<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Complaint\Repository\ComplaintRepository;

readonly class DeleteComplaintAction
{
    public function __construct(private ComplaintRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
