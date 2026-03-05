<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeOption;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfigurableProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create();
        $this->adminUser->givePermissionTo([
            'product-list', 'product-create', 'product-update', 'product-delete',
        ]);
    }

    #[Test]
    public function it_can_create_a_configurable_product(): void
    {
        $productData = [
            'type' => Product::TYPE_CONFIGURABLE,
            'title' => 'T-Shirt',
            'sku' => 'TSHIRT',
            'price' => 29.99,
            'stock' => 100,
            'status' => 'active',
            'configurable_attributes' => ['color', 'size'],
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'title' => 'T-Shirt',
            'type' => Product::TYPE_CONFIGURABLE,
        ]);
    }

    #[Test]
    public function it_can_generate_variants_for_configurable_product(): void
    {
        // Create configurable product
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'title' => 'T-Shirt',
            'sku' => 'TSHIRT',
            'configurable_attributes' => ['color'],
        ]);

        // Create color attribute
        $colorAttr = Attribute::factory()->create([
            'code' => 'color',
            'type' => 'text',
        ]);
        AttributeOption::create([
            'attribute_id' => $colorAttr->id,
            'value' => 'red',
            'label' => 'Red',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.variants.generate', $product), [
                'attributes' => ['color'],
            ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals(1, $product->fresh()->variants()->count());
    }

    #[Test]
    public function it_returns_error_when_generating_variants_for_non_configurable(): void
    {
        $simpleProduct = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.variants.generate', $simpleProduct), [
                'attributes' => ['color'],
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function it_can_update_variant_prices(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'configurable_attributes' => ['color'],
        ]);

        $variant = Product::factory()->create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $product->id,
            'price' => 20.00,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.variants.update', $product), [
                'prices' => [$variant->id => 25.99],
            ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals(25.99, $variant->fresh()->price);
    }

    #[Test]
    public function it_can_update_variant_stock(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        $variant = Product::factory()->create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $product->id,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.variants.update', $product), [
                'stocks' => [$variant->id => 50],
            ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals(50, $variant->fresh()->stock);
    }

    #[Test]
    public function it_can_delete_all_variants(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        Product::factory()->count(3)->create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $product->id,
        ]);

        $this->assertEquals(3, $product->variants()->count());

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.products.variants.delete', $product));

        $response->assertJson(['success' => true]);
        $this->assertEquals(0, $product->fresh()->variants()->count());
    }

    #[Test]
    public function it_can_find_variant_by_attributes(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'configurable_attributes' => ['color'],
        ]);

        $colorAttr = Attribute::factory()->create(['code' => 'color', 'type' => 'text']);

        $variant = Product::factory()->create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $product->id,
            'sku' => 'TSHIRT-RED',
        ]);

        $variant->attributeValues()->create([
            'attribute_id' => $colorAttr->id,
            'attributable_type' => Product::class,
            'attributable_id' => $variant->id,
            'text_value' => 'red',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.variant.by-attributes', $product), [
                'attributes' => ['color' => 'red'],
            ]);

        $response->assertJson([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'sku' => 'TSHIRT-RED',
            ],
        ]);
    }

    #[Test]
    public function it_shows_configurable_section_on_edit(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'configurable_attributes' => ['color', 'size'],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewHas('configurableAttributes');
        $response->assertViewHas('variants');
    }

    #[Test]
    public function it_does_not_show_configurable_section_for_simple_products(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewHas('configurableAttributes', []);
    }

    #[Test]
    public function it_deletes_variants_when_parent_is_deleted(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);

        $variant = Product::factory()->create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $product->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $variant->id]);
    }

    #[Test]
    public function variant_inherits_parent_details_on_create(): void
    {
        $parent = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
            'brand_id' => 5,
            'description' => 'Parent description',
        ]);

        $variant = Product::create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $parent->id,
            'title' => 'Variant',
            'sku' => 'VARIANT',
        ]);

        $this->assertEquals(5, $variant->brand_id);
        $this->assertEquals('Parent description', $variant->description);
    }
}
