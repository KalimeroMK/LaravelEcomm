<?php

declare(strict_types=1);

namespace Modules\Category\Actions;

use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

readonly class UpdateCategoryOrderAction
{
    public function __construct(private CategoryRepository $repository) {}

    /**
     * Update category order (nested set structure).
     *
     * @param  array<int, array{id: int, parent_id: int|null, order: int}>  $categories
     */
    public function execute(array $categories): void
    {
        foreach ($categories as $categoryData) {
            $category = $this->repository->findById($categoryData['id']);

            // Update parent if changed
            if ($category->parent_id !== $categoryData['parent_id']) {
                if ($categoryData['parent_id']) {
                    $parent = $this->repository->findById($categoryData['parent_id']);
                    $category->appendToNode($parent)->save();
                } else {
                    $category->makeRoot()->save();
                }
            }
        }
    }
}
