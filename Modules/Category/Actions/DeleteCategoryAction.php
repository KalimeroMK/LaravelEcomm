<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\Repository\CategoryRepository;

readonly class DeleteCategoryAction
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
