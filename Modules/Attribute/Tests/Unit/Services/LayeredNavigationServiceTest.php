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
        /** @var Category $category */
        $category = Category::factory()->create();

        // Create filterable attributes
        Attribute::factory()->create([
            'code' => 'color',
            'is_filterable' => true,
        ]);
        Attribute::factory()->create([
            'code' => 'size',
            'is_filterable' => true,
        ]);
        Attribute::factory()->create([
            'code' => 'brand',
            'is_filterable' => false, // Not filterable
        ]);

        $filterable = $this->service->getFilterableAttributes([$category->id]);

        $this->assertCount(2, $filterable);
        $this->assertTrue($filterable->contains('code', 'color'));
        $this->assertTrue($filterable->contains('code', 'size'));
        $this->assertFalse($filterable->contains('code', 'brand'));
    }

    #[Test]
    public function it_gets_available_filters_with_counts(): void
    {
        /** @var Category $category */
        $category = Category::factory()->create();

        // Create attribute with options
        /** @var Attribute $color */
        $color = Attribute::factory()->create([
            'code' => 'color',
            'name' => 'Color',
            'type' => 'select',
            'display' => 'color',
            'is_filterable' => true,
        ]);

        AttributeOption::create([
            'attribute_id' => $color->id,
            'value' => 'red',
            'label' => 'Red',
            'color_hex' => '#FF0000',
        ]);
        AttributeOption::create([
            'attribute_id' => $color->id,
            'value' => 'blue',
            'label' => 'Blue',
            'color_hex' => '#0000FF',
        ]);

        // Create products in category with attribute values
        /** @var Product $product1 */
        $product1 = Product::factory()->create();
        $product1->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product1->id,
            'attributable_type' => Product::class,
            'text_value' => 'red',
        ]);

        /** @var Product $product2 */
        $product2 = Product::factory()->create();
        $product2->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product2->id,
            'attributable_type' => Product::class,
            'text_value' => 'red',
        ]);

        /** @var Product $product3 */
        $product3 = Product::factory()->create();
        $product3->categories()->attach($category);
        AttributeValue::create([
            'attribute_id' => $color->id,
            'attributable_id' => $product3->id,
            'attributable_type' => Product::class,
            'text_value' => 'blue',
        ]);

        $filters = $this->service->getAvailableFilters([], [$category->id]);

        $this->assertArrayHasKey('attributes', $filters);
        $this->assertArrayHasKey('price_range', $filters);
    }

    #[Test]
    public function it_applies_filters_to_query(): void
    {
        /** @var Category $category */
        $category = Category::factory()->create();

        /** @var Attribute $color */
        $color = Attribute::factory()->create(['code' => 'color', 'is_filterable' => true]);
        /** @var Attribute $size */
        $size = Attribute::factory()->create(['code' => 'size', 'is_filterable' => true]);

        // Create products
        /** @var Product $product1 */
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

        /** @var Product $product2 */
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

        $result = $filtered->get();
        $this->assertCount(1, $result);
        $first = $result->first();
        $this->assertNotNull($first);
        $this->assertEquals($product1->id, $first->id);
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

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $activeFilters);
        $this->assertCount(2, $activeFilters); // color and size, not sort
    }

    #[Test]
    public function it_determines_correct_filter_type(): void
    {
        /** @var Attribute $colorAttr */
        $colorAttr = Attribute::factory()->create(['display' => 'color']);
        /** @var Attribute $buttonAttr */
        $buttonAttr = Attribute::factory()->create(['display' => 'button']);
        /** @var Attribute $selectAttr */
        $selectAttr = Attribute::factory()->create(['display' => 'select', 'type' => 'multiselect']);
        /** @var Attribute $defaultAttr */
        $defaultAttr = Attribute::factory()->create(['display' => 'text']);

        $this->assertEquals('swatch', $this->service->getFilterType($colorAttr));
        $this->assertEquals('button', $this->service->getFilterType($buttonAttr));
        $this->assertEquals('multiselect', $this->service->getFilterType($selectAttr));
        $this->assertEquals('default', $this->service->getFilterType($defaultAttr));
    }
}
