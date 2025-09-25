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
    $response->assertSee('Welcome');
});

test('product grid page loads', function () {
    Product::factory()->count(10)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get('/products');

    $response->assertStatus(200);
    $response->assertSee('Products');
});

test('search functionality works', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get('/search?q=Test');

    $response->assertStatus(200);
    $response->assertSee('Test Product');
});

test('category page displays products', function () {
    Product::factory()->count(5)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get("/category/{$this->category->slug}");

    $response->assertStatus(200);
    $response->assertSee($this->category->name);
});

test('brand page displays products', function () {
    Product::factory()->count(5)->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $response = $this->get("/brand/{$this->brand->slug}");

    $response->assertStatus(200);
    $response->assertSee($this->brand->name);
});

test('blog page loads', function () {
    Post::factory()->count(5)->create();

    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});

test('blog post detail loads', function () {
    $post = Post::factory()->create();

    $response = $this->get("/blog/{$post->slug}");

    $response->assertStatus(200);
    $response->assertSee($post->title);
});

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertSee('Contact');
});

test('about page loads', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertSee('About');
});
