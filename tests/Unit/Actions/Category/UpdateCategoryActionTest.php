<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\UpdateCategoryAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class UpdateCategoryActionTest extends ActionTestCase
{
    public function testExecuteUpdatesCategoryTitle(): void
    {
        $category = Category::factory()->create(['title' => 'Old Title']);

        $dto = new CategoryDTO(
            id: $category->id,
            title: 'New Title',
            parent_id: null,
        );

        $action = app(UpdateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('New Title', $result->title);
        $this->assertEquals($category->id, $result->id);
    }

    public function testExecuteChangesParentToAnotherCategory(): void
    {
        $parent1 = Category::factory()->create();
        $parent2 = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent1)->save();

        $dto = new CategoryDTO(
            id: $child->id,
            title: $child->title,
            parent_id: $parent2->id,
        );

        $action = app(UpdateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertEquals($parent2->id, $result->parent_id);
    }

    public function testExecuteChangesChildToRoot(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();

        $dto = new CategoryDTO(
            id: $child->id,
            title: $child->title,
            parent_id: null,
        );

        $action = app(UpdateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertNull($result->parent_id);
        $this->assertTrue($result->isRoot());
    }

    public function testExecuteChangesRootToChild(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();

        $dto = new CategoryDTO(
            id: $child->id,
            title: $child->title,
            parent_id: $parent->id,
        );

        $action = app(UpdateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertEquals($parent->id, $result->parent_id);
        $this->assertFalse($result->isRoot());
    }

    public function testExecuteKeepsParentWhenNotChanged(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();
        $originalParentId = $child->parent_id;

        $dto = new CategoryDTO(
            id: $child->id,
            title: 'Updated Title',
            parent_id: $originalParentId,
        );

        $action = app(UpdateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertEquals($originalParentId, $result->parent_id);
        $this->assertEquals('Updated Title', $result->title);
    }

    public function testExecuteThrowsExceptionForNonExistentCategory(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $dto = new CategoryDTO(
            id: 99999,
            title: 'Test',
            parent_id: null,
        );

        $action = app(UpdateCategoryAction::class);
        $action->execute($dto);
    }
}
