<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Illuminate\Support\Collection;
use Modules\Category\Repository\CategoryRepository;

readonly class GetAllCategoriesAction
{
    public function __construct(private CategoryRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
