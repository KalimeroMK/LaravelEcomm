<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Post\Actions\FindPostAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindPostActionTest extends ActionTestCase
{
    public function testExecuteFindsPostById(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $action = app(FindPostAction::class);
        $result = $action->execute($post->id);

        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
        $this->assertEquals($post->title, $result->title);
    }

    public function testExecuteReturnsPostWithRelations(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()
            ->hasAttached(\Modules\Category\Models\Category::factory()->count(2))
            ->hasAttached(\Modules\Tag\Models\Tag::factory()->count(3))
            ->create(['user_id' => $user->id]);

        $action = app(FindPostAction::class);
        $result = $action->execute($post->id);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
    }

    public function testExecuteThrowsExceptionForNonExistentPost(): void
    {
        $action = app(FindPostAction::class);
        
        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999);
    }

    public function testExecuteFindsDifferentPosts(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'First Post',
        ]);
        $post2 = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Second Post',
        ]);

        $action = app(FindPostAction::class);
        
        $result1 = $action->execute($post1->id);
        $result2 = $action->execute($post2->id);

        $this->assertEquals('First Post', $result1->title);
        $this->assertEquals('Second Post', $result2->title);
    }

    public function testExecuteReturnsCorrectPostData(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Title',
            'slug' => 'test-slug',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'status' => 'active',
        ]);

        $action = app(FindPostAction::class);
        $result = $action->execute($post->id);

        $this->assertEquals('Test Title', $result->title);
        $this->assertEquals('test-slug', $result->slug);
        $this->assertEquals('Test Summary', $result->summary);
        $this->assertEquals('Test Description', $result->description);
        $this->assertEquals('active', $result->status);
        $this->assertEquals($user->id, $result->user_id);
    }
}
