<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\Bundle;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->category = Category::factory()->create();
    $this->brand = Brand::factory()->create();
    $this->products = Product::factory()->count(3)->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);
});

test('admin can view bundles list', function () {
    Bundle::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/bundles');

    $response->assertStatus(200);
});

test('admin can create bundle', function () {
    $bundleData = [
        'name' => 'Test Bundle',
        'slug' => 'test-bundle',
        'description' => 'Test Description',
        'price' => 299.99,
        'products' => $this->products->pluck('id')->toArray(),
        'images' => [],
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/bundles', $bundleData);

    $response->assertRedirect();
    $this->assertDatabaseHas('bundles', [
        'name' => 'Test Bundle',
        'slug' => 'test-bundle',
    ]);
});

test('bundle detail page loads', function () {
    $bundle = Bundle::factory()->create();
    $bundle->products()->attach($this->products->pluck('id'));

    $response = $this->get(route('front.bundle-detail', $bundle->slug));

    $response->assertStatus(200);
    $response->assertSee($bundle->name, false);
});

test('bundle displays included products', function () {
    $bundle = Bundle::factory()->create();
    $bundle->products()->attach($this->products->pluck('id'));

    $response = $this->get(route('front.bundle-detail', $bundle->slug));

    $response->assertStatus(200);
    // Verify bundle name is displayed
    $response->assertSee($bundle->name, false);
    // Verify bundle has products attached
    $bundle->refresh();
    $bundle->load('products');
    expect($bundle->products->count())->toBe(3);
    // Verify "Products in this Bundle" text is displayed
    $response->assertSee('Products in this Bundle', false);
    // Verify products count is displayed
    $response->assertSee((string) $bundle->products->count(), false);
    // Verify at least one product title is displayed
    $firstProduct = $this->products->first();
    $firstProduct->refresh();
    // Check if product title exists in response (case-insensitive)
    $response->assertSeeText($firstProduct->title);
});

test('admin can edit bundle', function () {
    $bundle = Bundle::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/bundles/{$bundle->id}/edit");

    $response->assertStatus(200);
    $response->assertSee($bundle->name);
});

test('admin can update bundle', function () {
    $bundle = Bundle::factory()->create();

    $response = $this->actingAs($this->admin)
        ->put("/admin/bundles/{$bundle->id}", [
            'name' => 'Updated Bundle',
            'slug' => 'updated-bundle',
            'description' => 'Updated Description',
            'price' => 399.99,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('bundles', [
        'id' => $bundle->id,
        'name' => 'Updated Bundle',
    ]);
});

test('admin can delete bundle', function () {
    $bundle = Bundle::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/bundles/{$bundle->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('bundles', [
        'id' => $bundle->id,
    ]);
});
