<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Category\Models\Category;
use Modules\Front\Actions\BlogFilterAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogFilterActionTest extends ActionTestCase
{
    public function test_invoke_returns_all_active_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogFilterAction::class);

        // Act
        $result = $action([]);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('recantPosts', $result);
    }

    public function test_invoke_filters_by_search_term(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->create([
            'title' => 'Laravel Tutorial',
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        Post::factory()->create([
            'title' => 'Vue.js Guide',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogFilterAction::class);

        // Act
        $result = $action(['search' => 'Laravel']);

        // Assert
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_filters_by_category(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create(['slug' => 'tutorials']);
        $post = Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post->categories()->attach($category->id);

        Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogFilterAction::class);

        // Act
        $result = $action(['category' => 'tutorials']);

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

        $action = app(BlogFilterAction::class);

        // Act
        $result = $action([]);

        // Assert
        $this->assertCount(3, $result['recantPosts']);
    }

    public function test_invoke_only_returns_active_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        Post::factory()->count(3)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        Post::factory()->count(2)->create([
            'status' => 'inactive',
            'user_id' => $user->id,
        ]);

        $action = app(BlogFilterAction::class);

        // Act
        $result = $action([]);

        // Assert
        $this->assertEquals(3, $result['posts']->total());
    }
}
