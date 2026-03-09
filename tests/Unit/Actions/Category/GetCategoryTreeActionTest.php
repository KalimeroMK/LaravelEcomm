<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\GetCategoryTreeAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class GetCategoryTreeActionTest extends ActionTestCase
{
    public function testExecuteReturnsHierarchicalTree(): void
    {
        // Create parent categories
        $parent1 = Category::factory()->create(['parent_id' => null, 'title' => 'Parent 1']);
        $parent2 = Category::factory()->create(['parent_id' => null, 'title' => 'Parent 2']);
        
        // Create child categories
        Category::factory()->count(2)->create(['parent_id' => $parent1->id]);
        Category::factory()->create(['parent_id' => $parent2->id]);

        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result); // Two parents
        $this->assertTrue($result->every(fn ($cat) => $cat->parent_id === null));
    }

    public function testExecuteReturnsActiveCategoriesOnly(): void
    {
        Category::factory()->create(['status' => 'active', 'parent_id' => null]);
        Category::factory()->create(['status' => 'inactive', 'parent_id' => null]);

        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertEquals('active', $result->first()->status);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoCategories(): void
    {
        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
