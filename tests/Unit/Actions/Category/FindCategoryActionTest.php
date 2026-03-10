<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\FindCategoryAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class FindCategoryActionTest extends ActionTestCase
{
    public function testExecuteFindsCategoryById(): void
    {
        $category = Category::factory()->create(['title' => 'Test Category']);

        $action = app(FindCategoryAction::class);
        $result = $action->execute($category->id);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
        $this->assertEquals('Test Category', $result->title);
    }

    public function testExecuteFindsRootCategory(): void
    {
        $category = Category::factory()->create();

        $action = app(FindCategoryAction::class);
        $result = $action->execute($category->id);

        $this->assertTrue($result->isRoot());
    }

    public function testExecuteFindsChildCategory(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->appendToNode($parent)->save();

        $action = app(FindCategoryAction::class);
        $result = $action->execute($child->id);

        $this->assertEquals($parent->id, $result->parent_id);
    }

    public function testExecuteThrowsExceptionForNonExistentCategory(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $action = app(FindCategoryAction::class);
        $action->execute(99999);
    }
}
