<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Post\Actions\GetAllCategoriesAction;
use Tests\Unit\Actions\ActionTestCase;

class GetAllCategoriesActionTest extends ActionTestCase
{
    public function testExecuteReturnsCollection(): void
    {
        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testExecuteReturnsAllCategories(): void
    {
        Category::factory()->count(5)->create();

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(5, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoCategories(): void
    {
        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsCategoryModels(): void
    {
        Category::factory()->create(['title' => 'Test Category']);

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Category::class, $result->first());
        $this->assertEquals('Test Category', $result->first()->title);
    }

    public function testExecuteReturnsAllCategoriesWithCorrectData(): void
    {
        // Use factory states instead of setting status directly
        Category::factory()->active()->create(['title' => 'Category One']);
        Category::factory()->inactive()->create(['title' => 'Category Two']);
        Category::factory()->active()->create(['title' => 'Category Three']);

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        
        $titles = $result->pluck('title')->toArray();
        $this->assertContains('Category One', $titles);
        $this->assertContains('Category Two', $titles);
        $this->assertContains('Category Three', $titles);
    }

    public function testExecuteReturnsNestedCategories(): void
    {
        $parentCategory = Category::factory()->create(['title' => 'Parent']);
        Category::factory()->subcategory($parentCategory)->create(['title' => 'Child']);

        $action = app(GetAllCategoriesAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result);
    }
}
