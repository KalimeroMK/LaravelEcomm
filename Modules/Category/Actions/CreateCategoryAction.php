<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

class CreateCategoryAction
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoryDTO $dto): Category
    {
        return $this->repository->create([
            'name' => $dto->name,
            'parent_id' => $dto->parent_id,
            'description' => $dto->description,
        ]);
    }
}
