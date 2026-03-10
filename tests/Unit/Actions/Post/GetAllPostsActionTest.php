<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Database\Eloquent\Collection;
use Modules\Post\Actions\GetAllPostsAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllPostsActionTest extends ActionTestCase
{
    public function testExecuteReturnsCollection(): void
    {
        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testExecuteReturnsAllPosts(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(5)->create(['user_id' => $user->id]);

        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertCount(5, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoPosts(): void
    {
        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsPostModels(): void
    {
        $user = User::factory()->create();
        Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Post Title',
        ]);

        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Post::class, $result->first());
        $this->assertEquals('Test Post Title', $result->first()->title);
    }

    public function testExecuteReturnsPostsWithRelations(): void
    {
        $user = User::factory()->create();
        Post::factory()
            ->count(3)
            ->hasAttached(\Modules\Category\Models\Category::factory()->count(2))
            ->hasAttached(\Modules\Tag\Models\Tag::factory()->count(2))
            ->create(['user_id' => $user->id]);

        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Post::class, $result->first());
    }

    public function testExecuteReturnsPostsWithDifferentStatuses(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Post::factory()->create(['user_id' => $user->id, 'status' => 'inactive']);
        Post::factory()->create(['user_id' => $user->id, 'status' => 'draft']);

        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        
        $statuses = $result->pluck('status')->toArray();
        $this->assertContains('active', $statuses);
        $this->assertContains('inactive', $statuses);
        $this->assertContains('draft', $statuses);
    }

    public function testExecuteReturnsPostsFromDifferentUsers(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Post::factory()->create(['user_id' => $user1->id, 'title' => 'User1 Post']);
        Post::factory()->create(['user_id' => $user2->id, 'title' => 'User2 Post']);

        $action = app(GetAllPostsAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result);
        
        $titles = $result->pluck('title')->toArray();
        $this->assertContains('User1 Post', $titles);
        $this->assertContains('User2 Post', $titles);
    }
}
