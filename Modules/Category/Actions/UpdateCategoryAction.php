<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;

readonly class UpdateCategoryAction
{
    public function execute(CategoryDTO $dto): Category
    {
        $category = Category::findOrFail($dto->id);
        $category->update([
            'name' => $dto->name,
            'parent_id' => $dto->parent_id,
            'description' => $dto->description,
        ]);

        return $category;
    }
}
