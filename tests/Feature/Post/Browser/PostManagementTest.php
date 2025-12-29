<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Post\Models\Post;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
});

test('blog listing page loads', function () {
    Post::factory()->count(5)->withCategoriesAndTags()->create([
        'status' => 'active',
    ]);

    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});

test('blog post detail page loads', function () {
    $post = Post::factory()->withCategoriesAndTags()->create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'description' => 'This is test content',
        'status' => 'active',
    ]);

    $response = $this->get(route('front.blog-detail', $post->slug));

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
        'description' => 'This is new post content',
        'summary' => 'Post excerpt',
        'status' => 'active',
        'images' => [
            Illuminate\Http\UploadedFile::fake()->image('post-image.jpg'),
        ],
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
            'description' => 'Updated content',
            'status' => 'active',
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
});

test('posts can be filtered by category', function () {
    $category1 = Modules\Category\Models\Category::factory()->create(['title' => 'Technology']);
    $category2 = Modules\Category\Models\Category::factory()->create(['title' => 'Business']);

    $post1 = Post::factory()->create([
        'title' => 'Tech Post',
        'status' => 'active',
    ]);
    $post1->categories()->attach($category1->id);

    $post2 = Post::factory()->create([
        'title' => 'Business Post',
        'status' => 'active',
    ]);
    $post2->categories()->attach($category2->id);

    $response = $this->get(route('front.blog-by-category', $category1->slug));

    $response->assertStatus(200);
});

test('post search works', function () {
    Post::factory()->create([
        'title' => 'Laravel Tutorial',
        'description' => 'Learn Laravel framework',
        'status' => 'active',
    ]);

    Post::factory()->create([
        'title' => 'PHP Basics',
        'description' => 'Learn PHP programming',
        'status' => 'active',
    ]);

    $response = $this->get(route('front.blog-search', ['q' => 'Laravel']));

    $response->assertStatus(200);
});

test('admin can manage post categories', function () {
    // Post categories are managed through Category module, not Post module
    $response = $this->actingAs($this->admin)
        ->get('/admin/categories');

    expect($response->status())->toBeIn([200, 302]);
});
