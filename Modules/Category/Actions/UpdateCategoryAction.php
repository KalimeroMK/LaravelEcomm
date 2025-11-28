<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Repository\CategoryRepository;

readonly class UpdateCategoryAction
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoryDTO $dto): Model
    {
        $category = $this->repository->findById($dto->id);

        // Update title
        $category->title = $dto->title;

        // Handle parent change for nested set
        if ($dto->parent_id !== $category->parent_id) {
            if ($dto->parent_id) {
                $parent = $this->repository->findById($dto->parent_id);
                $category->appendToNode($parent)->save();
            } else {
                $category->makeRoot()->save();
            }
        } else {
            $category->save();
        }

        return $category->fresh();
    }
}
