<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->brand = Brand::factory()->create();
});

test('homepage loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('E-commerce Website');
});

test('product grid page loads', function () {
    Product::factory()->count(10)->create([
        'brand_id' => $this->brand->id,
    ])->each(function ($product) {
        $product->categories()->attach($this->category->id);
    });

    $response = $this->get('/product-grids');

    $response->assertStatus(200);
    $response->assertSee('Products');
});

test('search functionality works', function () {
    $product = Product::factory()->create([
        'title' => 'Test Product',
        'brand_id' => $this->brand->id,
    ]);
    $product->categories()->attach($this->category->id);

    $response = $this->post('/product/search', ['search' => 'Test']);

    $response->assertStatus(200);
});

test('category page displays products', function () {
    Product::factory()->count(5)->create([
        'brand_id' => $this->brand->id,
    ])->each(function ($product) {
        $product->categories()->attach($this->category->id);
    });

    $response = $this->get("/product-cat/{$this->category->slug}");

    $response->assertStatus(200);
});

test('brand page displays products', function () {
    Product::factory()->count(5)->create([
        'brand_id' => $this->brand->id,
    ])->each(function ($product) {
        $product->categories()->attach($this->category->id);
    });

    $response = $this->get("/product-brand/{$this->brand->slug}");

    $response->assertStatus(200);
});

test('blog page loads', function () {
    Post::factory()->count(5)->create();

    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});

test('blog post detail loads', function () {
    $post = Post::factory()->create();

    $response = $this->get("/blog-detail/{$post->slug}");

    $response->assertStatus(200);
});

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertSee('Contact');
});

test('about page loads', function () {
    $response = $this->get('/about-us');

    $response->assertStatus(200);
    $response->assertSee('About');
});
