<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

readonly class CreateCategoryAction
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoryDTO $dto): Category
    {
        $category = new Category([
            'title' => $dto->title,
            'parent_id' => $dto->parent_id,
        ]);

        if ($dto->parent_id) {
            $parent = $this->repository->findById($dto->parent_id);
            $category->appendToNode($parent)->save();
        } else {
            $category->makeRoot()->save();
        }

        return $category->fresh();
    }
}
