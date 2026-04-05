<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Front\Actions\GetAdvancedSearchAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetAdvancedSearchActionTest extends ActionTestCase
{
    public function test_invoke_returns_expected_structure(): void
    {
        $action = app(GetAdvancedSearchAction::class);
        $result = $action('test', []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertArrayHasKey('availableFilters', $result);
        $this->assertArrayHasKey('totalResults', $result);
        $this->assertArrayHasKey('searchPerformed', $result);
    }

    public function test_invoke_with_empty_query_does_not_perform_search(): void
    {
        $action = app(GetAdvancedSearchAction::class);
        $result = $action('', []);

        $this->assertFalse($result['searchPerformed']);
        $this->assertEquals(0, $result['totalResults']);
    }

    public function test_invoke_with_query_performs_search(): void
    {
        Product::factory()->create(['title' => 'Test Widget', 'status' => 'active']);

        $action = app(GetAdvancedSearchAction::class);
        $result = $action('Test', []);

        $this->assertTrue($result['searchPerformed']);
        $this->assertEquals('Test', $result['query']);
    }

    public function test_invoke_passes_filters_through(): void
    {
        $filters = ['price_min' => 10, 'price_max' => 100];

        $action = app(GetAdvancedSearchAction::class);
        $result = $action('test', $filters);

        $this->assertEquals($filters, $result['filters']);
    }

    public function test_invoke_returns_available_filters_with_price_range(): void
    {
        Product::factory()->create(['price' => 50, 'status' => 'active']);
        Product::factory()->create(['price' => 200, 'status' => 'active']);

        $action = app(GetAdvancedSearchAction::class);
        $result = $action('', []);

        $this->assertArrayHasKey('price_range', $result['availableFilters']);
        $this->assertArrayHasKey('min', $result['availableFilters']['price_range']);
        $this->assertArrayHasKey('max', $result['availableFilters']['price_range']);
    }

    public function test_invoke_returns_available_brands_in_filters(): void
    {
        $brand = Brand::factory()->create(['status' => 'active']);
        Product::factory()->create(['brand_id' => $brand->id, 'status' => 'active']);

        $action = app(GetAdvancedSearchAction::class);
        $result = $action('', []);

        $this->assertArrayHasKey('brands', $result['availableFilters']);
    }

    public function test_invoke_returns_available_categories_in_filters(): void
    {
        $category = Category::factory()->create(['status' => 'active']);
        $product = Product::factory()->create(['status' => 'active']);
        $category->products()->attach($product->id);

        $action = app(GetAdvancedSearchAction::class);
        $result = $action('', []);

        $this->assertArrayHasKey('categories', $result['availableFilters']);
    }
}
