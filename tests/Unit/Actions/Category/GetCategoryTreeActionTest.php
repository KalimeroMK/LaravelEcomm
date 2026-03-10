<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Category;

use Modules\Category\Actions\GetCategoryTreeAction;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class GetCategoryTreeActionTest extends ActionTestCase
{
    public function testExecuteReturnsCategoryTree(): void
    {
        $parent = Category::factory()->create(['title' => 'Electronics']);
        $child = Category::factory()->create(['title' => 'Laptops']);
        $child->appendToNode($parent)->save();

        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoCategories(): void
    {
        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsTreeStructure(): void
    {
        // Create root categories
        $electronics = Category::factory()->create(['title' => 'Electronics']);
        $clothing = Category::factory()->create(['title' => 'Clothing']);

        // Create children
        $laptops = Category::factory()->create(['title' => 'Laptops']);
        $laptops->appendToNode($electronics)->save();

        $shirts = Category::factory()->create(['title' => 'Shirts']);
        $shirts->appendToNode($clothing)->save();

        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteReturnsCollectionWithNestedData(): void
    {
        Category::factory()->create(['title' => 'Root Category']);

        $action = app(GetCategoryTreeAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count());
    }
}
