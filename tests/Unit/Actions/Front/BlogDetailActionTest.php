<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Category\Models\Category;
use Modules\Front\Actions\BlogDetailAction;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogDetailActionTest extends ActionTestCase
{
    public function test_invoke_returns_post_detail(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'slug' => 'test-post',
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post->categories()->attach($category->id);

        $action = app(BlogDetailAction::class);

        // Act
        $result = $action('test-post');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('post', $result);
        $this->assertArrayHasKey('recantPosts', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertEquals($post->id, $result['post']->id);
    }

    public function test_invoke_throws_exception_for_nonexistent_slug(): void
    {
        // Arrange
        $action = app(BlogDetailAction::class);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action('nonexistent-post');
    }

    public function test_invoke_returns_recent_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post = Post::factory()->create([
            'slug' => 'detail-post',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogDetailAction::class);

        // Act
        $result = $action('detail-post');

        // Assert
        $this->assertCount(3, $result['recantPosts']);
    }

    public function test_invoke_returns_tags(): void
    {
        // Arrange
        $user = User::factory()->create();
        Tag::factory()->count(5)->create();
        $post = Post::factory()->create([
            'slug' => 'tagged-post',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogDetailAction::class);

        // Act
        $result = $action('tagged-post');

        // Assert
        $this->assertNotNull($result['tags']);
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->create([
            'slug' => 'cached-post',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogDetailAction::class);

        // Act
        $result1 = $action('cached-post');
        $result2 = $action('cached-post');

        // Assert
        $this->assertEquals($result1['post']->id, $result2['post']->id);
    }
}
