<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Front\Actions\GetSearchSuggestionsAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetSearchSuggestionsActionTest extends ActionTestCase
{
    public function test_invoke_returns_expected_structure(): void
    {
        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('test');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('popular_terms', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('brands', $result);
        $this->assertArrayHasKey('suggested_query', $result);
    }

    public function test_invoke_returns_matching_product_terms(): void
    {
        Product::factory()->create(['title' => 'Gaming Laptop Pro', 'status' => 'active']);
        Product::factory()->create(['title' => 'Gaming Mouse', 'status' => 'active']);
        Product::factory()->create(['title' => 'Unrelated Item', 'status' => 'active']);

        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('Gaming');

        $this->assertContains('Gaming Laptop Pro', $result['popular_terms']);
        $this->assertContains('Gaming Mouse', $result['popular_terms']);
    }

    public function test_invoke_returns_matching_categories(): void
    {
        Category::factory()->create(['title' => 'Electronics', 'status' => 'active']);
        Category::factory()->create(['title' => 'Electric Guitars', 'status' => 'active']);

        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('Elec');

        $this->assertNotEmpty($result['categories']);
    }

    public function test_invoke_limits_results(): void
    {
        Product::factory()->count(10)->create(['status' => 'active']);

        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('');

        $this->assertLessThanOrEqual(5, count($result['popular_terms']));
        $this->assertLessThanOrEqual(3, count($result['categories']));
        $this->assertLessThanOrEqual(3, count($result['brands']));
    }

    public function test_invoke_applies_query_corrections(): void
    {
        $action = app(GetSearchSuggestionsAction::class);

        $result = $action('laptop');
        $this->assertEquals('laptop computer', $result['suggested_query']);

        $result = $action('phone');
        $this->assertEquals('smartphone', $result['suggested_query']);
    }

    public function test_invoke_passes_through_uncorrected_query(): void
    {
        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('headphones');

        $this->assertEquals('headphones', $result['suggested_query']);
    }

    public function test_invoke_returns_matching_brands(): void
    {
        Brand::factory()->create(['title' => 'Nike Sports', 'status' => 'active']);
        Brand::factory()->create(['title' => 'Adidas', 'status' => 'active']);

        $action = app(GetSearchSuggestionsAction::class);
        $result = $action('Nike');

        $this->assertContains('Nike Sports', $result['brands']);
    }
}
