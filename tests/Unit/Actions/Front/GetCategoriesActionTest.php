<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Models\Category;
use Modules\Front\Actions\GetCategoriesAction;
use Tests\Unit\Actions\ActionTestCase;

class GetCategoriesActionTest extends ActionTestCase
{
    public function test_invoke_returns_categories_array(): void
    {
        Category::factory()->count(3)->create(['status' => 'active']);

        $action = app(GetCategoriesAction::class);
        $result = $action();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('categories', $result);
    }

    public function test_invoke_returns_only_active_categories(): void
    {
        Category::factory()->count(3)->create(['status' => 'active']);
        Category::factory()->count(2)->create(['status' => 'inactive']);

        $action = app(GetCategoriesAction::class);
        $result = $action();

        foreach ($result['categories'] as $category) {
            $this->assertEquals('active', $category->status);
        }
    }

    public function test_invoke_caches_result(): void
    {
        Category::factory()->count(2)->create(['status' => 'active']);

        $action = app(GetCategoriesAction::class);
        $result1 = $action();

        // Add another category after first call
        Category::factory()->create(['status' => 'active']);

        $result2 = $action();

        // Should return same cached result
        $this->assertCount(count($result1['categories']), $result2['categories']);
    }

    public function test_invoke_returns_empty_when_no_active_categories(): void
    {
        Category::factory()->count(2)->create(['status' => 'inactive']);

        $action = app(GetCategoriesAction::class);
        $result = $action();

        $this->assertEmpty($result['categories']);
    }

    public function test_invoke_cache_is_tagged_correctly(): void
    {
        Category::factory()->create(['status' => 'active']);

        $action = app(GetCategoriesAction::class);
        $action();

        $this->assertTrue(Cache::has('front_categories_page'));
    }
}
