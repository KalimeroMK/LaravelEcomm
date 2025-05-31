<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class DeleteNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
