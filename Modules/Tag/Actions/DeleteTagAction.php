<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Modules\Tag\Repository\TagRepository;

readonly class DeleteTagAction
{
    public function __construct(private TagRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
