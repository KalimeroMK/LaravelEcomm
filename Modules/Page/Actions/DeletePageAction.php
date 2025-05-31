<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\Repository\PageRepository;

readonly class DeletePageAction
{
    public function __construct(private PageRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
