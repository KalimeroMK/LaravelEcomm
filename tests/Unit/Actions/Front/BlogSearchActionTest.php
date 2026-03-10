<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Http\Request;
use Modules\Front\Actions\BlogSearchAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogSearchActionTest extends ActionTestCase
{
    public function test_invoke_returns_search_results(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->create([
            'title' => 'Laravel Best Practices',
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        Post::factory()->create([
            'title' => 'Vue.js Tutorial',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $request = new Request(['search' => 'Laravel']);
        $action = app(BlogSearchAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('recantPosts', $result);
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_returns_empty_for_no_matches(): void
    {
        // Arrange
        $request = new Request(['search' => 'nonexistent']);
        $action = app(BlogSearchAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertEquals(0, $result['posts']->total());
    }

    public function test_invoke_searches_in_title(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->create([
            'title' => 'Advanced Laravel Tips',
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        Post::factory()->create([
            'title' => 'React Basics',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $request = new Request(['search' => 'Laravel']);
        $action = app(BlogSearchAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_returns_recent_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $request = new Request(['search' => 'test']);
        $action = app(BlogSearchAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertCount(3, $result['recantPosts']);
    }

    public function test_invoke_returns_paginated_results(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(15)->create([
            'title' => 'Test Post',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $request = new Request(['search' => 'Test']);
        $action = app(BlogSearchAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertCount(10, $result['posts']->items());
    }
}
