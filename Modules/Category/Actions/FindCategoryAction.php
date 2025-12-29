<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

readonly class FindCategoryAction
{
    public function __construct(private CategoryRepository $repository) {}

    public function execute(int $id): Category
    {
        return $this->repository->findById($id);
    }
}
