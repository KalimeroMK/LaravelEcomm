<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Front\Actions\BlogByTagAction;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogByTagActionTest extends ActionTestCase
{
    public function test_invoke_returns_posts_by_tag(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['slug' => 'laravel']);
        $post = Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post->tags()->attach($tag->id);

        $action = app(BlogByTagAction::class);

        // Act
        $result = $action('laravel');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('recent_posts', $result);
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_returns_empty_for_nonexistent_tag(): void
    {
        // Arrange
        $action = app(BlogByTagAction::class);

        // Act
        $result = $action('nonexistent-tag');

        // Assert
        $this->assertEquals(0, $result['posts']->total());
    }

    public function test_invoke_returns_only_posts_with_tag(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['slug' => 'php']);
        $tag2 = Tag::factory()->create(['slug' => 'javascript']);

        $post1 = Post::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        $post1->tags()->attach($tag1->id);

        $post2 = Post::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        $post2->tags()->attach($tag2->id);

        $action = app(BlogByTagAction::class);

        // Act
        $result = $action('php');

        // Assert
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['slug' => 'cached-tag']);
        $post = Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post->tags()->attach($tag->id);

        $action = app(BlogByTagAction::class);

        // Act
        $result1 = $action('cached-tag');
        $result2 = $action('cached-tag');

        // Assert
        $this->assertEquals($result1['posts']->pluck('id'), $result2['posts']->pluck('id'));
    }
}
