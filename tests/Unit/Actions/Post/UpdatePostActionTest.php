<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Category\Models\Category;
use Modules\Post\Actions\UpdatePostAction;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdatePostActionTest extends ActionTestCase
{
    public function testExecuteUpdatesPostSuccessfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'summary' => 'Old Summary',
        ]);

        $dto = new PostDTO(
            id: $post->id,
            title: 'New Title',
            slug: 'new-slug',
            summary: 'New Summary',
            description: 'New Description',
            status: 'inactive',
            user_id: $user->id,
            categories: [],
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('New Title', $result->title);
        $this->assertEquals('new-slug', $result->slug);
        $this->assertEquals('New Summary', $result->summary);
        $this->assertEquals('New Description', $result->description);
        $this->assertEquals('inactive', $result->status);
    }

    public function testExecuteThrowsExceptionForNonExistentPost(): void
    {
        $user = User::factory()->create();
        
        $dto = new PostDTO(
            id: 99999,
            title: 'Test Title',
            slug: 'test-slug',
            summary: 'Test Summary',
            description: 'Test Description',
            status: 'active',
            user_id: $user->id,
            categories: [],
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        
        // The action throws ModelNotFoundException when post doesn't exist
        $this->expectException(ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function testExecuteUpdatesPostCategories(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $newCategories = Category::factory()->count(3)->create();

        $dto = new PostDTO(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: $newCategories->pluck('id')->toArray(),
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $this->assertCount(3, $result->fresh()->categories);
    }

    public function testExecuteUpdatesPostTags(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $newTags = Tag::factory()->count(4)->create();

        $dto = new PostDTO(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: [],
            tags: $newTags->pluck('id')->toArray(),
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $this->assertCount(4, $result->fresh()->tags);
    }

    public function testExecuteUpdatesPostCategoriesAndTags(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $newCategories = Category::factory()->count(2)->create();
        $newTags = Tag::factory()->count(3)->create();

        $dto = new PostDTO(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: $newCategories->pluck('id')->toArray(),
            tags: $newTags->pluck('id')->toArray(),
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $freshPost = $result->fresh();
        $this->assertCount(2, $freshPost->categories);
        $this->assertCount(3, $freshPost->tags);
    }

    public function testExecuteReplacesExistingCategories(): void
    {
        $user = User::factory()->create();
        $oldCategories = Category::factory()->count(2)->create();
        $post = Post::factory()
            ->hasAttached($oldCategories)
            ->create(['user_id' => $user->id]);
        
        $newCategories = Category::factory()->count(3)->create();

        $this->assertCount(2, $post->fresh()->categories);

        $dto = new PostDTO(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: $newCategories->pluck('id')->toArray(),
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        // Should have 3 new categories, not 5 (old + new)
        $this->assertCount(3, $result->fresh()->categories);
    }

    public function testExecuteUpdatesWithNullDescription(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'description' => 'Old Description',
        ]);

        $dto = new PostDTO(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: null,
            status: $post->status,
            user_id: $post->user_id,
            categories: [],
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $this->assertNull($result->description);
    }

    public function testExecuteReturnsPostModel(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $dto = new PostDTO(
            id: $post->id,
            title: 'Updated Title',
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: [],
            tags: [],
        );

        $action = app(UpdatePostAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
    }
}
