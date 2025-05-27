<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

readonly class GetAllCategoriesAction
{
    public function __construct(private CategoryRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
