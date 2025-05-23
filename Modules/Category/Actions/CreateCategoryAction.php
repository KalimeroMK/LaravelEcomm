<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;

readonly class CreateCategoryAction
{
    public function execute(CategoryDTO $dto): Category
    {
        return Category::create([
            'name' => $dto->name,
            'parent_id' => $dto->parent_id,
            'description' => $dto->description,
        ]);
    }
}
