<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeOption;
use Modules\Product\Models\Product;
use Modules\Product\Services\ConfigurableProductService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfigurableProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ConfigurableProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConfigurableProductService();
    }

    #[Test]
    public function it_generates_variants_from_configurable_attributes(): void
    {
        // Create configurable product
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'sku' => 'CONFIG-TSHIRT',
        ]);

        // Create attributes
        $colorAttr = Attribute::factory()->create([
            'code' => 'color',
            'name' => 'Color',
            'type' => 'select',
            'display' => 'color',
            'is_configurable' => true,
        ]);

        $sizeAttr = Attribute::factory()->create([
            'code' => 'size',
            'name' => 'Size',
            'type' => 'select',
            'display' => 'button',
            'is_configurable' => true,
        ]);

        // Create options
        AttributeOption::create([
            'attribute_id' => $colorAttr->id,
            'value' => 'red',
            'label' => 'Red',
            'color_hex' => '#FF0000',
        ]);
        AttributeOption::create([
            'attribute_id' => $colorAttr->id,
            'value' => 'blue',
            'label' => 'Blue',
            'color_hex' => '#0000FF',
        ]);

        AttributeOption::create([
            'attribute_id' => $sizeAttr->id,
            'value' => 's',
            'label' => 'S',
        ]);
        AttributeOption::create([
            'attribute_id' => $sizeAttr->id,
            'value' => 'm',
            'label' => 'M',
        ]);

        // Attach attributes to product
        $product->configurableAttributes()->attach([$colorAttr->id, $sizeAttr->id]);

        // Generate variants
        $this->service->generateVariants($product);

        // Assert 4 variants created (2 colors × 2 sizes)
        $this->assertCount(4, $product->fresh()->variants);

        // Assert variant SKUs
        $skus = $product->variants->pluck('sku')->toArray();
        $this->assertContains('CONFIG-TSHIRT-RED-S', $skus);
        $this->assertContains('CONFIG-TSHIRT-RED-M', $skus);
        $this->assertContains('CONFIG-TSHIRT-BLUE-S', $skus);
        $this->assertContains('CONFIG-TSHIRT-BLUE-M', $skus);
    }

    #[Test]
    public function it_finds_variant_by_attribute_combination(): void
    {
        // Create configurable product with variants
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'sku' => 'CONFIG-SHOES',
        ]);

        $colorAttr = Attribute::factory()->create([
            'code' => 'color',
            'is_configurable' => true,
        ]);

        $product->configurableAttributes()->attach($colorAttr->id);

        // Create variant
        $variant = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'parent_id' => $product->id,
            'sku' => 'CONFIG-SHOES-BLACK',
        ]);

        $variant->attributeValues()->create([
            'attribute_id' => $colorAttr->id,
            'text_value' => 'black',
        ]);

        // Test finding variant
        $found = $this->service->getVariantByAttributes($product, ['color' => 'black']);

        $this->assertNotNull($found);
        $this->assertEquals($variant->id, $found->id);
    }

    #[Test]
    public function it_updates_variant_prices(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        $variant = Product::factory()->create([
            'parent_id' => $product->id,
            'price' => 10.00,
        ]);

        $prices = [$variant->id => 25.99];

        $this->service->updateVariantPrices($product, $prices);

        $this->assertEquals(25.99, $variant->fresh()->price);
    }

    #[Test]
    public function it_deletes_all_variants(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        Product::factory()->count(3)->create([
            'parent_id' => $product->id,
        ]);

        $this->assertCount(3, $product->fresh()->variants);

        $this->service->deleteVariants($product);

        $this->assertCount(0, $product->fresh()->variants);
    }

    #[Test]
    public function it_calculates_price_range_of_variants(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        Product::factory()->create(['parent_id' => $product->id, 'price' => 10.00]);
        Product::factory()->create(['parent_id' => $product->id, 'price' => 20.00]);
        Product::factory()->create(['parent_id' => $product->id, 'price' => 30.00]);

        $range = $this->service->getPriceRange($product);

        $this->assertEquals(10.00, $range['min']);
        $this->assertEquals(30.00, $range['max']);
    }
}
