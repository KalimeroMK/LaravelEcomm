<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->brand = Brand::factory()->create();
});

test('product listing page loads', function () {
    Product::factory()->count(5)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get('/products');

    $response->assertStatus(200);
    $response->assertSee('Products');
});

test('product detail page loads', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get("/products/{$product->slug}");

    $response->assertStatus(200);
    $response->assertSee($product->name);
});

test('product search works', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get('/search?q=Test');

    $response->assertStatus(200);
    $response->assertSee('Test Product');
});

test('product filtering by category works', function () {
    Product::factory()->count(3)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get("/category/{$this->category->slug}");

    $response->assertStatus(200);
    $response->assertSee($this->category->name);
});

test('product filtering by brand works', function () {
    Product::factory()->count(3)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get("/brand/{$this->brand->slug}");

    $response->assertStatus(200);
    $response->assertSee($this->brand->name);
});
