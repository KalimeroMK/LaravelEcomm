<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Message\Repository\MessageRepository;

readonly class DeleteMessageAction
{
    public function __construct(private MessageRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
