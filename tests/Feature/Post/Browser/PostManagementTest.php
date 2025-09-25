<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Post\Models\Category;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->category = Category::factory()->create();
});

test('blog listing page loads', function () {
    Post::factory()->count(5)->create([
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);

    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});

test('blog post detail page loads', function () {
    $post = Post::factory()->create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'This is test content',
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);

    $response = $this->get("/blog/{$post->slug}");

    $response->assertStatus(200);
    $response->assertSee('Test Post');
    $response->assertSee('This is test content');
});

test('admin can view posts list', function () {
    Post::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/posts');

    $response->assertStatus(200);
    $response->assertSee('Posts');
});

test('admin can create post', function () {
    $postData = [
        'title' => 'New Post',
        'slug' => 'new-post',
        'content' => 'This is new post content',
        'excerpt' => 'Post excerpt',
        'category_id' => $this->category->id,
        'status' => 'published',
        'featured_image' => 'post-image.jpg',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/posts', $postData);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'title' => 'New Post',
        'slug' => 'new-post',
    ]);
});

test('admin can edit post', function () {
    $post = Post::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/posts/{$post->id}/edit");

    $response->assertStatus(200);
    $response->assertSee($post->title);
});

test('admin can update post', function () {
    $post = Post::factory()->create([
        'title' => 'Old Title',
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
    ]);
});

test('admin can delete post', function () {
    $post = Post::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/posts/{$post->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

test('only published posts show on blog', function () {
    Post::factory()->create([
        'title' => 'Published Post',
        'status' => 'published',
    ]);

    Post::factory()->create([
        'title' => 'Draft Post',
        'status' => 'draft',
    ]);

    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Published Post');
    $response->assertDontSee('Draft Post');
});

test('posts can be filtered by category', function () {
    $category1 = Category::factory()->create(['name' => 'Technology']);
    $category2 = Category::factory()->create(['name' => 'Business']);

    Post::factory()->create([
        'title' => 'Tech Post',
        'category_id' => $category1->id,
        'status' => 'published',
    ]);

    Post::factory()->create([
        'title' => 'Business Post',
        'category_id' => $category2->id,
        'status' => 'published',
    ]);

    $response = $this->get("/blog/category/{$category1->slug}");

    $response->assertStatus(200);
    $response->assertSee('Tech Post');
    $response->assertDontSee('Business Post');
});

test('post search works', function () {
    Post::factory()->create([
        'title' => 'Laravel Tutorial',
        'content' => 'Learn Laravel framework',
        'status' => 'published',
    ]);

    Post::factory()->create([
        'title' => 'PHP Basics',
        'content' => 'Learn PHP programming',
        'status' => 'published',
    ]);

    $response = $this->get('/blog/search?q=Laravel');

    $response->assertStatus(200);
    $response->assertSee('Laravel Tutorial');
    $response->assertDontSee('PHP Basics');
});

test('admin can manage post categories', function () {
    $response = $this->actingAs($this->admin)
        ->get('/admin/posts/categories');

    $response->assertStatus(200);
    $response->assertSee('Categories');
});

test('admin can create post category', function () {
    $categoryData = [
        'name' => 'Technology',
        'slug' => 'technology',
        'description' => 'Tech related posts',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/posts/categories', $categoryData);

    $response->assertRedirect();
    $this->assertDatabaseHas('post_categories', [
        'name' => 'Technology',
        'slug' => 'technology',
    ]);
});
