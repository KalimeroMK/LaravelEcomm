<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeOption;
use Modules\Product\Models\Product;
use Tests\TestCase;

class ProductAttributeTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_single_and_multi_value_attributes()
    {
        // Create attributes
        $color = Attribute::factory()->create(['code' => 'color', 'display' => 'checkbox', 'type' => 'string']);
        $size = Attribute::factory()->create(['code' => 'size', 'display' => 'multiselect', 'type' => 'string']);
        $material = Attribute::factory()->create(['code' => 'material', 'display' => 'input', 'type' => 'string']);

        // Create options for color and size
        $red = AttributeOption::create(['attribute_id' => $color->id, 'value' => 'Red']);
        $blue = AttributeOption::create(['attribute_id' => $color->id, 'value' => 'Blue']);
        $small = AttributeOption::create(['attribute_id' => $size->id, 'value' => 'S']);
        $medium = AttributeOption::create(['attribute_id' => $size->id, 'value' => 'M']);

        // Prepare product data
        $productData = [
            'title' => 'Test Product',
            'sku' => 'TESTSKU',
            'price' => 100,
            'stock' => 10,
            'attributes' => [
                'color' => ['Red', 'Blue'], // Multi-value
                'size' => ['S', 'M'],       // Multi-value
                'material' => 'Cotton',     // Single value
            ],
            'category' => [], // Adjust as needed for your app
        ];

        // Simulate POST request
        $response = $this->post(route('products.store'), $productData);
        $response->assertStatus(302); // Redirect after success

        // Assert product exists
        $product = Product::where('title', 'Test Product')->first();
        $this->assertNotNull($product);

        // Assert attribute values
        $this->assertDatabaseHas('attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $color->id,
            'string_value' => 'Red',
        ]);
        $this->assertDatabaseHas('attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $color->id,
            'string_value' => 'Blue',
        ]);
        $this->assertDatabaseHas('attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'string_value' => 'S',
        ]);
        $this->assertDatabaseHas('attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'string_value' => 'M',
        ]);
        $this->assertDatabaseHas('attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $material->id,
            'string_value' => 'Cotton',
        ]);
    }
}
