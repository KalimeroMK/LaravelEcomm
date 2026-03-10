<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Category\Models\Category;
use Modules\Front\Actions\BlogByCategoryAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class BlogByCategoryActionTest extends ActionTestCase
{
    public function test_invoke_returns_posts_by_category(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create(['slug' => 'tech-news']);
        $post = Post::factory()->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);
        $post->categories()->attach($category->id);

        $action = app(BlogByCategoryAction::class);

        // Act
        $result = $action('tech-news');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('recantPosts', $result);
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_returns_empty_for_nonexistent_category(): void
    {
        // Arrange
        $action = app(BlogByCategoryAction::class);

        // Act
        $result = $action('nonexistent-category');

        // Assert
        $this->assertEquals(0, $result['posts']->total());
    }

    public function test_invoke_returns_only_posts_in_category(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category1 = Category::factory()->create(['slug' => 'tech']);
        $category2 = Category::factory()->create(['slug' => 'lifestyle']);

        $post1 = Post::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        $post1->categories()->attach($category1->id);

        $post2 = Post::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        $post2->categories()->attach($category2->id);

        $action = app(BlogByCategoryAction::class);

        // Act
        $result = $action('tech');

        // Assert
        $this->assertEquals(1, $result['posts']->total());
    }

    public function test_invoke_returns_recent_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create(['slug' => 'tech']);
        Post::factory()->count(5)->create([
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $action = app(BlogByCategoryAction::class);

        // Act
        $result = $action('tech');

        // Assert
        $this->assertCount(3, $result['recantPosts']);
    }
}
