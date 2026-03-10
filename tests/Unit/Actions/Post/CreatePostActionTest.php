<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Modules\Category\Models\Category;
use Modules\Post\Actions\CreatePostAction;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreatePostActionTest extends ActionTestCase
{
    public function testExecuteCreatesPostWithValidData(): void
    {
        $user = User::factory()->create();
        
        $dto = new PostDTO(
            id: null,
            title: 'Test Post Title',
            slug: 'test-post-title',
            summary: 'Test post summary',
            description: 'Test post description',
            status: 'active',
            user_id: $user->id,
            categories: [],
            tags: [],
        );

        $action = app(CreatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('Test Post Title', $result->title);
        $this->assertEquals('test-post-title', $result->slug);
        $this->assertEquals('Test post summary', $result->summary);
        $this->assertEquals('Test post description', $result->description);
        $this->assertEquals('active', $result->status);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertDatabaseHas('posts', ['title' => 'Test Post Title']);
    }

    public function testExecuteCreatesPostWithCategories(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create();
        
        $dto = new PostDTO(
            id: null,
            title: 'Post With Categories',
            slug: 'post-with-categories',
            summary: 'Test summary',
            description: 'Test description',
            status: 'active',
            user_id: $user->id,
            categories: $categories->pluck('id')->toArray(),
            tags: [],
        );

        $action = app(CreatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertCount(3, $result->fresh()->categories);
    }

    public function testExecuteCreatesPostWithTags(): void
    {
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        
        $dto = new PostDTO(
            id: null,
            title: 'Post With Tags',
            slug: 'post-with-tags',
            summary: 'Test summary',
            description: 'Test description',
            status: 'inactive',
            user_id: $user->id,
            categories: [],
            tags: $tags->pluck('id')->toArray(),
        );

        $action = app(CreatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertCount(2, $result->fresh()->tags);
    }

    public function testExecuteCreatesPostWithCategoriesAndTags(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $tags = Tag::factory()->count(3)->create();
        
        $dto = new PostDTO(
            id: null,
            title: 'Complete Post',
            slug: 'complete-post',
            summary: 'Test summary',
            description: 'Test description',
            status: 'active',
            user_id: $user->id,
            categories: $categories->pluck('id')->toArray(),
            tags: $tags->pluck('id')->toArray(),
        );

        $action = app(CreatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $freshPost = $result->fresh();
        $this->assertCount(2, $freshPost->categories);
        $this->assertCount(3, $freshPost->tags);
    }

    public function testExecuteCreatesPostWithNullDescription(): void
    {
        $user = User::factory()->create();
        
        $dto = new PostDTO(
            id: null,
            title: 'Post Without Description',
            slug: 'post-without-description',
            summary: 'Test summary',
            description: null,
            status: 'active',
            user_id: $user->id,
            categories: [],
            tags: [],
        );

        $action = app(CreatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertNull($result->description);
        $this->assertDatabaseHas('posts', [
            'title' => 'Post Without Description',
            'description' => null,
        ]);
    }
}
