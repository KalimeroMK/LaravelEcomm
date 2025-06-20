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
        $category->update([
            'title' => $dto->title,
            'parent_id' => $dto->parent_id,
        ]);

        return $category;
    }
}
