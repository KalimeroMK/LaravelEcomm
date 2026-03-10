<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\CreateCategoryAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class CreateCategoryActionTest extends ActionTestCase
{
    public function testExecuteCreatesRootCategory(): void
    {
        $dto = new CategoryDTO(
            id: null,
            title: 'Electronics',
            parent_id: null,
        );

        $action = app(CreateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Electronics', $result->title);
        $this->assertNull($result->parent_id);
        $this->assertTrue($result->isRoot());
    }

    public function testExecuteCreatesChildCategory(): void
    {
        $parent = Category::factory()->create();

        $dto = new CategoryDTO(
            id: null,
            title: 'Laptops',
            parent_id: $parent->id,
        );

        $action = app(CreateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Laptops', $result->title);
        $this->assertEquals($parent->id, $result->parent_id);
        $this->assertFalse($result->isRoot());
    }

    public function testExecuteCreatesNestedChildCategory(): void
    {
        $grandparent = Category::factory()->create();
        $parent = Category::factory()->create();
        $parent->appendToNode($grandparent)->save();

        $dto = new CategoryDTO(
            id: null,
            title: 'Gaming Laptops',
            parent_id: $parent->id,
        );

        $action = app(CreateCategoryAction::class);
        $result = $action->execute($dto);

        $this->assertEquals($parent->id, $result->parent_id);
        $this->assertFalse($result->isRoot());
    }

    public function testExecuteCreatesMultipleRootCategories(): void
    {
        $dto1 = new CategoryDTO(id: null, title: 'Electronics', parent_id: null);
        $dto2 = new CategoryDTO(id: null, title: 'Clothing', parent_id: null);

        $action = app(CreateCategoryAction::class);
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        $this->assertTrue($result1->isRoot());
        $this->assertTrue($result2->isRoot());
        $this->assertNotEquals($result1->id, $result2->id);
    }
}
