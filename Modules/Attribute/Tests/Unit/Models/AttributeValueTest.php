<?php

declare(strict_types=1);

namespace Modules\Attribute\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AttributeValueTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_typed_values(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        // Test text value
        /** @var Attribute $textAttr */
        $textAttr = Attribute::factory()->create(['type' => 'text']);
        $textValue = AttributeValue::create([
            'attribute_id' => $textAttr->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'text_value' => 'Sample text',
        ]);

        $this->assertEquals('Sample text', $textValue->getValue());

        // Test boolean value
        /** @var Attribute $boolAttr */
        $boolAttr = Attribute::factory()->create(['type' => 'boolean']);
        $boolValue = AttributeValue::create([
            'attribute_id' => $boolAttr->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'boolean_value' => true,
        ]);

        $this->assertTrue($boolValue->getValue());

        // Test integer value
        /** @var Attribute $intAttr */
        $intAttr = Attribute::factory()->create(['type' => 'integer']);
        $intValue = AttributeValue::create([
            'attribute_id' => $intAttr->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'integer_value' => 42,
        ]);

        $this->assertEquals(42, $intValue->getValue());
    }

    #[Test]
    public function it_has_polymorphic_relation(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        /** @var Attribute $attribute */
        $attribute = Attribute::factory()->create();

        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'text_value' => 'test',
        ]);

        $this->assertInstanceOf(Product::class, $value->attributable);
        /** @var Product $attributable */
        $attributable = $value->attributable;
        $this->assertEquals($product->id, $attributable->id);
    }

    #[Test]
    public function it_can_set_and_get_value_dynamically(): void
    {
        /** @var Attribute $attribute */
        $attribute = Attribute::factory()->create(['type' => 'string']);
        /** @var Product $product */
        $product = Product::factory()->create();

        $value = new AttributeValue([
            'attribute_id' => $attribute->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
        ]);

        $value->setValue('Dynamic string');
        $value->save();

        $freshValue = $value->fresh();
        $this->assertNotNull($freshValue);
        $this->assertEquals('Dynamic string', $freshValue->getValue());
    }

    #[Test]
    public function it_belongs_to_attribute(): void
    {
        /** @var Attribute $attribute */
        $attribute = Attribute::factory()->create(['name' => 'Test Attribute']);
        /** @var Product $product */
        $product = Product::factory()->create();

        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'text_value' => 'test',
        ]);

        $this->assertInstanceOf(Attribute::class, $value->attribute);
        $this->assertEquals('Test Attribute', $value->attribute->name);
    }

    #[Test]
    public function it_can_get_value_by_code(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        /** @var Attribute $attribute */
        $attribute = Attribute::factory()->create(['code' => 'color', 'type' => 'string']);

        AttributeValue::create([
            'attribute_id' => $attribute->id,
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'string_value' => 'red',
        ]);

        $value = $product->getAttributeValueByCode('color');
        $this->assertEquals('red', $value);
    }
}
