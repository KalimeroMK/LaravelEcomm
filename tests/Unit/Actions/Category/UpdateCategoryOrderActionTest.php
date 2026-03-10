<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\UpdateCategoryOrderAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class UpdateCategoryOrderActionTest extends ActionTestCase
{
    public function testExecuteUpdatesCategoryParent(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();

        $categories = [
            ['id' => $child->id, 'parent_id' => $parent->id, 'order' => 1],
        ];

        $action = app(UpdateCategoryOrderAction::class);
        $action->execute($categories);

        $child->refresh();
        $this->assertEquals($parent->id, $child->parent_id);
    }

    public function testExecuteMovesCategoryToRoot(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();

        $categories = [
            ['id' => $child->id, 'parent_id' => null, 'order' => 1],
        ];

        $action = app(UpdateCategoryOrderAction::class);
        $action->execute($categories);

        $child->refresh();
        $this->assertNull($child->parent_id);
    }

    public function testExecuteUpdatesMultipleCategories(): void
    {
        $parent1 = Category::factory()->create();
        $parent2 = Category::factory()->create();
        $child1 = Category::factory()->create();
        $child2 = Category::factory()->create();

        $categories = [
            ['id' => $child1->id, 'parent_id' => $parent1->id, 'order' => 1],
            ['id' => $child2->id, 'parent_id' => $parent2->id, 'order' => 2],
        ];

        $action = app(UpdateCategoryOrderAction::class);
        $action->execute($categories);

        $child1->refresh();
        $child2->refresh();
        $this->assertEquals($parent1->id, $child1->parent_id);
        $this->assertEquals($parent2->id, $child2->parent_id);
    }

    public function testExecuteKeepsParentWhenNotChanged(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();

        $categories = [
            ['id' => $child->id, 'parent_id' => $parent->id, 'order' => 5],
        ];

        $action = app(UpdateCategoryOrderAction::class);
        $action->execute($categories);

        $child->refresh();
        $this->assertEquals($parent->id, $child->parent_id);
    }

    public function testExecuteChangesParentFromOneToAnother(): void
    {
        $oldParent = Category::factory()->create();
        $newParent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($oldParent)->save();

        $categories = [
            ['id' => $child->id, 'parent_id' => $newParent->id, 'order' => 1],
        ];

        $action = app(UpdateCategoryOrderAction::class);
        $action->execute($categories);

        $child->refresh();
        $this->assertEquals($newParent->id, $child->parent_id);
    }
}
