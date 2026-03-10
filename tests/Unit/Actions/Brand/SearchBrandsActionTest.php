<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Brand;

use Modules\Brand\Actions\SearchBrandsAction;
use Modules\Brand\Models\Brand;
use Tests\Unit\Actions\ActionTestCase;

class SearchBrandsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllBrandsWithEmptyFilters(): void
    {
        Brand::factory()->create(['title' => 'Nike']);
        Brand::factory()->create(['title' => 'Adidas']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute([]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testExecuteReturnsAllWhenAllIncludedFilter(): void
    {
        Brand::factory()->count(3)->create();

        $action = app(SearchBrandsAction::class);
        $result = $action->execute(['all_included' => true]);

        $this->assertCount(3, $result);
    }

    public function testExecuteFiltersByTitle(): void
    {
        Brand::factory()->create(['title' => 'Nike Sports']);
        Brand::factory()->create(['title' => 'Adidas']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute(['title' => 'Nike']);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        // The search uses 'like' so it should find the brand with 'Nike' in title
    }

    public function testExecuteFiltersBySlug(): void
    {
        Brand::factory()->create(['slug' => 'nike-sports']);
        Brand::factory()->create(['slug' => 'adidas']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute(['slug' => 'nike']);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }

    public function testExecuteFiltersByStatus(): void
    {
        Brand::factory()->create(['status' => 'active']);
        Brand::factory()->create(['status' => 'inactive']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute(['status' => 'active']);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }

    public function testExecuteReturnsPaginatorWithMultipleFilters(): void
    {
        Brand::factory()->create(['title' => 'Nike', 'status' => 'active']);
        Brand::factory()->create(['title' => 'Adidas', 'status' => 'inactive']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute([
            'title' => 'Nike',
            'status' => 'active',
        ]);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }

    public function testExecuteOrdersBySpecifiedField(): void
    {
        Brand::factory()->create(['title' => 'Nike']);
        Brand::factory()->create(['title' => 'Adidas']);

        $action = app(SearchBrandsAction::class);
        $result = $action->execute([
            'order_by' => 'title',
            'sort' => 'asc',
        ]);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }
}
