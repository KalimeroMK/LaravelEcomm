<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Actions\SyncProductAttributesAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class SyncProductAttributesActionTest extends ActionTestCase
{
    public function testExecuteSyncsAttributeWithStringType(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'material',
            'type' => 'string',
        ]);

        $attributes = [
            'material' => ['value' => 'Cotton'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $attribute->id,
            'string_value' => 'Cotton',
        ]);
    }

    public function testExecuteSyncsAttributeWithIntegerType(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'warranty_months',
            'type' => 'integer',
        ]);

        $attributes = [
            'warranty_months' => ['value' => '24'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $attribute->id,
            'integer_value' => 24,
        ]);
    }

    public function testExecuteSyncsAttributeWithFloatType(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'weight',
            'type' => 'float',
        ]);

        $attributes = [
            'weight' => ['value' => '1.5'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $attribute->id,
            'float_value' => 1.5,
        ]);
    }

    public function testExecuteSyncsAttributeWithBooleanType(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'is_waterproof',
            'type' => 'boolean',
        ]);

        $attributes = [
            'is_waterproof' => ['value' => 'true'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $attribute->id,
            'boolean_value' => true,
        ]);
    }

    public function testExecuteSyncsAttributeWithDateType(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'release_date',
            'type' => 'date',
        ]);

        $attributes = [
            'release_date' => ['value' => '2024-03-15'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        // Check record exists with correct attribute and value is set
        $value = AttributeValue::where('attributable_id', $product->id)
            ->where('attribute_id', $attribute->id)
            ->first();

        $this->assertNotNull($value);
        $this->assertNotNull($value->date_value);
    }

    public function testExecutePrefersOptionOverValue(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'color',
            'type' => 'string',
        ]);

        $attributes = [
            'color' => ['option' => 'Red', 'value' => 'Blue'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $attribute->id,
            'string_value' => 'Red',
        ]);
    }

    public function testExecuteDeletesOldAttributeValues(): void
    {
        $product = Product::factory()->create();
        $oldAttribute = Attribute::factory()->create(['code' => 'old_attr', 'type' => 'string']);

        // Create existing attribute value
        AttributeValue::create([
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'product_id' => $product->id,
            'attribute_id' => $oldAttribute->id,
            'string_value' => 'Old Value',
        ]);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attribute_id' => $oldAttribute->id,
        ]);

        // Sync with new attribute
        $newAttribute = Attribute::factory()->create(['code' => 'new_attr', 'type' => 'string']);
        $attributes = ['new_attr' => ['value' => 'New Value']];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseMissing('attribute_values', [
            'attributable_id' => $product->id,
            'attribute_id' => $oldAttribute->id,
        ]);
    }

    public function testExecuteSkipsNonExistentAttributes(): void
    {
        $product = Product::factory()->create();

        $attributes = [
            'non_existent_attribute' => ['value' => 'Test'],
        ];

        // Should not throw any exception
        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertTrue(true); // Test passes if we reach this point
    }

    public function testExecuteSkipsEmptyValues(): void
    {
        $product = Product::factory()->create();
        $attribute = Attribute::factory()->create([
            'code' => 'optional_field',
            'type' => 'string',
        ]);

        $attributes = [
            'optional_field' => ['value' => ''],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseMissing('attribute_values', [
            'attributable_id' => $product->id,
            'attribute_id' => $attribute->id,
        ]);
    }

    public function testExecuteSyncsMultipleAttributes(): void
    {
        $product = Product::factory()->create();
        $colorAttr = Attribute::factory()->create(['code' => 'color', 'type' => 'string']);
        $sizeAttr = Attribute::factory()->create(['code' => 'size', 'type' => 'string']);

        $attributes = [
            'color' => ['value' => 'Blue'],
            'size' => ['value' => 'Large'],
        ];

        SyncProductAttributesAction::execute($product, $attributes);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $colorAttr->id,
            'string_value' => 'Blue',
        ]);

        $this->assertDatabaseHas('attribute_values', [
            'attributable_id' => $product->id,
            'attributable_type' => Product::class,
            'attribute_id' => $sizeAttr->id,
            'string_value' => 'Large',
        ]);
    }
}
