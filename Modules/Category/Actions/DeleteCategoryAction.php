<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\Models\Category;

readonly class DeleteCategoryAction
{
    public function execute(int $id): bool
    {
        $category = Category::findOrFail($id);

        return $category->delete();
    }
}
