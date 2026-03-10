<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Modules\Post\Actions\DeletePostAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeletePostActionTest extends ActionTestCase
{
    public function testExecuteDeletesPostSuccessfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);

        $action = app(DeletePostAction::class);
        $action->execute($post->id);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function testExecuteDeletesPostWithRelations(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()
            ->hasAttached(\Modules\Category\Models\Category::factory()->count(2))
            ->hasAttached(\Modules\Tag\Models\Tag::factory()->count(2))
            ->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('category_post', ['post_id' => $post->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id]);

        $action = app(DeletePostAction::class);
        $action->execute($post->id);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function testExecuteDoesNotThrowForNonExistentPost(): void
    {
        $action = app(DeletePostAction::class);
        
        // Should not throw an exception
        $action->execute(99999);
        
        $this->assertTrue(true); // Test passes if we reach this point
    }

    public function testExecuteDeletesMultiplePostsIndependently(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('posts', ['id' => $post1->id]);
        $this->assertDatabaseHas('posts', ['id' => $post2->id]);

        $action = app(DeletePostAction::class);
        $action->execute($post1->id);

        $this->assertDatabaseMissing('posts', ['id' => $post1->id]);
        $this->assertDatabaseHas('posts', ['id' => $post2->id]);
    }
}
