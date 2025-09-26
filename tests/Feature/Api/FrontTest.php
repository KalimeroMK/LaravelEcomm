<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Product\Models\Product;
use Modules\Category\Models\Category;
use Modules\Brand\Models\Brand;
use Modules\Post\Models\Post;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FrontTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * test front homepage.
     */
    #[Test]
    public function test_front_homepage(): void
    {
        $response = $this->json('GET', '/api/v1/');
        
        $response->assertStatus(200);
    }

    /**
     * test product detail.
     */
    #[Test]
    public function test_product_detail(): void
    {
        $product = Product::factory()->create([
            'status' => 'active',
        ]);
        
        $response = $this->json('GET', "/api/v1/product-detail/{$product->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test product search.
     */
    #[Test]
    public function test_product_search(): void
    {
        $product = Product::factory()->create([
            'title' => 'Test Product',
            'status' => 'active',
        ]);
        
        $response = $this->json('POST', '/api/v1/product/search', [
            'search' => 'Test',
        ]);
        
        $response->assertStatus(200);
    }

    /**
     * test product category.
     */
    #[Test]
    public function test_product_category(): void
    {
        $category = Category::factory()->create([
            'status' => 'active',
        ]);
        
        $product = Product::factory()->create([
            'status' => 'active',
        ]);
        
        $product->categories()->attach($category->id);
        
        $response = $this->json('GET', "/api/v1/product-cat/{$category->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test product brand.
     */
    #[Test]
    public function test_product_brand(): void
    {
        $brand = Brand::factory()->create([
            'status' => 'active',
        ]);
        
        $product = Product::factory()->create([
            'status' => 'active',
            'brand_id' => $brand->id,
        ]);
        
        $response = $this->json('GET', "/api/v1/product-brand/{$brand->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test blog.
     */
    #[Test]
    public function test_blog(): void
    {
        $response = $this->json('GET', '/api/v1/blog');
        
        $response->assertStatus(200);
    }

    /**
     * test blog detail.
     */
    #[Test]
    public function test_blog_detail(): void
    {
        $post = Post::factory()->create([
            'status' => 'active',
        ]);
        
        $response = $this->json('GET', "/api/v1/blog-detail/{$post->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test blog search.
     */
    #[Test]
    public function test_blog_search(): void
    {
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'status' => 'active',
        ]);
        
        $response = $this->json('GET', '/api/v1/blog/search', [
            'search' => 'Test',
        ]);
        
        $response->assertStatus(200);
    }

    /**
     * test blog by category.
     */
    #[Test]
    public function test_blog_by_category(): void
    {
        $category = Category::factory()->create([
            'status' => 'active',
        ]);
        
        $response = $this->json('GET', "/api/v1/blog-cat/{$category->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test blog by tag.
     */
    #[Test]
    public function test_blog_by_tag(): void
    {
        $tag = \Modules\Tag\Models\Tag::factory()->create([
            'status' => 'active',
        ]);
        
        $response = $this->json('GET', "/api/v1/blog-tag/{$tag->slug}");
        
        $response->assertStatus(200);
    }

    /**
     * test product deal.
     */
    #[Test]
    public function test_product_deal(): void
    {
        $response = $this->json('GET', '/api/v1/product/deal');
        
        $response->assertStatus(200);
    }
}
