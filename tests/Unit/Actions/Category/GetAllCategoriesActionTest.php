<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\GetAllCategoriesAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class GetAllCategoriesActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllCategories(): void
    {
        Category::factory()->create(['title' => 'Category 1']);
        Category::factory()->create(['title' => 'Category 2']);
        Category::factory()->create(['title' => 'Category 3']);

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsCollectionOfCategoryModels(): void
    {
        Category::factory()->count(2)->create();

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertInstanceOf(Category::class, $result->first());
    }

    public function testExecuteReturnsEmptyCollectionWhenNoCategories(): void
    {
        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsCategoriesWithHierarchy(): void
    {
        $parent = Category::factory()->create(['title' => 'Parent']);
        $child = Category::factory()->create(['title' => 'Child']);
        $child->appendToNode($parent)->save();

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result);
    }
}
