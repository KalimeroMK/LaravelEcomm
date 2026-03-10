<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\DeleteCategoryAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class DeleteCategoryActionTest extends ActionTestCase
{
    public function testExecuteDeletesCategory(): void
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $action = app(DeleteCategoryAction::class);
        $action->execute($categoryId);

        // Category uses SoftDeletes, so verify soft deletion
        $this->assertSoftDeleted('categories', ['id' => $categoryId]);
    }

    public function testExecuteDeletesCategoryWithChildren(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();

        $action = app(DeleteCategoryAction::class);
        $action->execute($parent->id);

        $this->assertSoftDeleted('categories', ['id' => $parent->id]);
    }

    public function testExecuteDeletesMultipleCategories(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $action = app(DeleteCategoryAction::class);
        $action->execute($category1->id);
        $action->execute($category2->id);

        $this->assertSoftDeleted('categories', ['id' => $category1->id]);
        $this->assertSoftDeleted('categories', ['id' => $category2->id]);
    }

    public function testExecuteSoftDeletesCategory(): void
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $action = app(DeleteCategoryAction::class);
        $action->execute($categoryId);

        // Category should be soft deleted (not actually removed from DB)
        $this->assertDatabaseHas('categories', ['id' => $categoryId]);
        $this->assertNotNull(Category::withTrashed()->find($categoryId)->deleted_at);
    }
}
