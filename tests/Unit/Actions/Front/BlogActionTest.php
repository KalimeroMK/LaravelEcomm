<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Front\Actions\BlogAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogActionTest extends ActionTestCase
{
    public function test_invoke_returns_blog_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('recantPosts', $result);
    }

    public function test_invoke_returns_paginated_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(15)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogAction::class);

        // Act
        $result = $action();

        // Assert - should return up to 9 posts per page
        $this->assertLessThanOrEqual(9, $result['posts']->count());
        $this->assertGreaterThan(0, $result['posts']->count());
    }

    public function test_invoke_returns_recent_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogAction::class);

        // Act
        $result = $action();

        // Assert
        $this->assertCount(3, $result['recantPosts']);
    }

    public function test_invoke_only_returns_active_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        $activePosts = Post::factory()->count(3)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $inactivePosts = Post::factory()->count(2)->create([
            'status' => 'inactive',
            'user_id' => $user->id,
        ]);

        $action = app(BlogAction::class);

        // Act
        $result = $action();

        // Assert - total should include only active posts (but may include more from other tests/seeding)
        foreach ($result['posts'] as $post) {
            $this->assertEquals('active', $post->status);
        }
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogAction::class);

        // Act
        $result1 = $action();
        $result2 = $action();

        // Assert
        $this->assertEquals($result1['posts']->pluck('id'), $result2['posts']->pluck('id'));
    }
}
