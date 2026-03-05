<?php

declare(strict_types=1);

namespace Modules\Attribute\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeOption;
use Modules\Attribute\Models\AttributeValue;
use Modules\Attribute\Services\LayeredNavigationService;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LayeredNavigationServiceTest extends TestCase
{
    use RefreshDatabase;

    private LayeredNavigationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LayeredNavigationService();
    }

    #[Test]
    public function it_gets_filterable_attributes_for_category(): void
    {
        $category = Category::factory()->create();

        // Create filterable attributes
        $color = Attribute::factory()->create([
            'code' => 'color',
            'is_filterable' => true,
        ]);
        $size = Attribute::factory()->create([
            'code' => 'size',
            'is_filterable' => true,
        ]);
        $brand = Attribute::factory()->create([
            'code' => 'brand',
            'is_filterable' => false, // Not filterable
        ]);

        $filterable = $this->service->getFilterableAttributes($category);

        $this->assertCount(2, $filterable);
        $this->assertTrue($filterable->contains('code', 'color'));
        $this->assertTrue($filterable->contains('code', 'size'));
        $this->assertFalse($filterable->contains('code', 'brand'));
    }

    #[Test]
    public function it_gets_available_filters_with_counts(): void
    {
        $category = Category::factory()->create();

        // Create attribute with options
        $color = Attribute::factory()->create([
            'code' => 'color',
            'name' => 'Color',
            'type' => 'select',
            'display' => 'color',
            'is_filterable' => true,
        ]);

        $redOption = AttributeOption::create([
            'attribute_id' => $color->id,
            'value' => 'red',
            'label' => 'Red',
            'color_hex' => '#FF0000',
        ]);
        $blueOption = AttributeOption::create([
            'attribute_id' => $color->id,
            'value' => 'blue',
            'label' => 'Blue',
            'color_hex' => '#0000FF',
        ]);

        // Create products in category with attribute values
        $product1 = Product::factory()->create();
        $product1->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product1->id,
            'attributable_type' => Product::class,
            'text_value' => 'red',
        ]);

        $product2 = Product::factory()->create();
        $product2->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product2->id,
            'attributable_type' => Product::class,
            'text_value' => 'red',
        ]);

        $product3 = Product::factory()->create();
        $product3->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product3->id,
            'attributable_type' => Product::class,
            'text_value' => 'blue',
        ]);

        $filters = $this->service->getAvailableFilters($category);

        $this->assertCount(1, $filters);
        $this->assertEquals('color', $filters[0]['code']);
        $this->assertEquals('Color', $filters[0]['name']);
        $this->assertEquals('swatch', $filters[0]['type']);
        $this->assertCount(2, $filters[0]['options']);

        // Check counts
        $redOption = collect($filters[0]['options'])->firstWhere('value', 'red');
        $this->assertEquals(2, $redOption['count']);

        $blueOption = collect($filters[0]['options'])->firstWhere('value', 'blue');
        $this->assertEquals(1, $blueOption['count']);
    }

    #[Test]
    public function it_applies_filters_to_query(): void
    {
        $category = Category::factory()->create();

        $color = Attribute::factory()->create(['code' => 'color', 'is_filterable' => true]);
        $size = Attribute::factory()->create(['code' => 'size', 'is_filterable' => true]);

        // Create products
        $product1 = Product::factory()->create();
        $product1->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product1->id,
            'attributable_type' => Product::class,
            'text_value' => 'red',
        ]);
        AttributeValue::create([
            'attribute_id' => $size->id,
            'attributable_id' => $product1->id,
            'attributable_type' => Product::class,
            'text_value' => 'm',
        ]);

        $product2 = Product::factory()->create();
        $product2->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product2->id,
            'attributable_type' => Product::class,
            'text_value' => 'blue',
        ]);
        AttributeValue::create([
            'attribute_id' => $size->id,
            'attributable_id' => $product2->id,
            'attributable_type' => Product::class,
            'text_value' => 'l',
        ]);

        // Apply filters
        $query = Product::query();
        $query->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));

        $activeFilters = ['color' => ['red']];
        $filtered = $this->service->applyFilters($query, $activeFilters);

        $this->assertCount(1, $filtered->get());
        $this->assertEquals($product1->id, $filtered->first()->id);
    }

    #[Test]
    public function it_returns_active_filters_from_request(): void
    {
        $request = new \Illuminate\Http\Request([
            'color' => 'red,blue',
            'size' => 'm',
            'sort' => 'price',
        ]);

        $activeFilters = $this->service->getActiveFilters($request);

        $this->assertEquals(['red', 'blue'], $activeFilters['color']);
        $this->assertEquals('m', $activeFilters['size']);
        $this->assertArrayNotHasKey('sort', $activeFilters);
    }

    #[Test]
    public function it_determines_correct_filter_type(): void
    {
        $colorAttr = Attribute::factory()->create(['display' => 'color']);
        $buttonAttr = Attribute::factory()->create(['display' => 'button']);
        $selectAttr = Attribute::factory()->create(['display' => 'select', 'type' => 'multiselect']);
        $defaultAttr = Attribute::factory()->create(['display' => 'text']);

        $this->assertEquals('swatch', $this->service->getFilterType($colorAttr));
        $this->assertEquals('button', $this->service->getFilterType($buttonAttr));
        $this->assertEquals('multiselect', $this->service->getFilterType($selectAttr));
        $this->assertEquals('default', $this->service->getFilterType($defaultAttr));
    }
}
