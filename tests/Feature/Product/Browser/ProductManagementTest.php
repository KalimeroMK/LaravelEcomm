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
    Product::factory()->count(5)->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get(route('front.product-grids'));

    $response->assertStatus(200);
});

test('product detail page loads', function () {
    $product = Product::factory()->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get(route('front.product-detail', $product->slug));

    $response->assertStatus(200);
    $response->assertSee($product->title);
});

test('product search works', function () {
    $product = Product::factory()->withCategories()->create([
        'title' => 'Test Product',
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->post(route('front.product-search'), ['search' => 'Test']);

    $response->assertStatus(200);
    $response->assertSee('Test Product');
});

test('product filtering by category works', function () {
    Product::factory()->count(3)->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get(route('front.product-cat', $this->category->slug));

    $response->assertStatus(200);
});

test('product filtering by brand works', function () {
    Product::factory()->count(3)->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get(route('front.product-brand', $this->brand->slug));

    $response->assertStatus(200);
});
