<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Repository\CategoryRepository;

readonly class UpdateCategoryAction
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    public function execute(CategoryDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'name' => $dto->name,
            'parent_id' => $dto->parent_id,
            'description' => $dto->description,
        ]);
    }
}
