<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;

readonly class GetCategoryTreeAction
{
    public function execute(): Collection
    {
        $tree = Category::getTree();

        // Convert array to Collection
        return collect($tree);
    }
}
