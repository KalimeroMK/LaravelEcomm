<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Post\Actions\DeletePostMediaAction;
use Modules\Post\Models\Post;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeletePostMediaActionTest extends ActionTestCase
{
    public function testExecuteDeletesMediaSuccessfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        // Add media to the post
        $tempFile = tempnam(sys_get_temp_dir(), 'test_media');
        file_put_contents($tempFile, 'test content');
        $media = $post->addMedia($tempFile)
            ->toMediaCollection('post');
        
        $this->assertDatabaseHas('media', ['id' => $media->id, 'model_id' => $post->id]);

        $action = app(DeletePostMediaAction::class);
        $action->execute($post->id, $media->id);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        
        @unlink($tempFile);
    }

    public function testExecuteThrowsExceptionForNonExistentPost(): void
    {
        $action = app(DeletePostMediaAction::class);

        $this->expectException(ModelNotFoundException::class);
        
        $action->execute(99999, 1);
    }

    public function testExecuteDoesNothingForNonExistentMedia(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $action = app(DeletePostMediaAction::class);
        
        // Should not throw exception when media doesn't exist
        $action->execute($post->id, 99999);
        
        $this->assertTrue(true); // Test passes if we reach this point
    }

    public function testExecuteOnlyDeletesSpecifiedMedia(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        // Add two media items
        $tempFile1 = tempnam(sys_get_temp_dir(), 'test_media1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'test_media2');
        file_put_contents($tempFile1, 'test content 1');
        file_put_contents($tempFile2, 'test content 2');
        
        $media1 = $post->addMedia($tempFile1)->toMediaCollection('post');
        $media2 = $post->addMedia($tempFile2)->toMediaCollection('post');
        
        $this->assertDatabaseHas('media', ['id' => $media1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id]);

        $action = app(DeletePostMediaAction::class);
        $action->execute($post->id, $media1->id);

        $this->assertDatabaseMissing('media', ['id' => $media1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id]);
        
        @unlink($tempFile1);
        @unlink($tempFile2);
    }

    public function testExecuteOnlyDeletesMediaBelongingToSpecifiedPost(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);
        
        // Add media to both posts
        $tempFile1 = tempnam(sys_get_temp_dir(), 'test_media1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'test_media2');
        file_put_contents($tempFile1, 'test content 1');
        file_put_contents($tempFile2, 'test content 2');
        
        $media1 = $post1->addMedia($tempFile1)->toMediaCollection('post');
        $media2 = $post2->addMedia($tempFile2)->toMediaCollection('post');
        
        $this->assertDatabaseHas('media', ['id' => $media1->id, 'model_id' => $post1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id, 'model_id' => $post2->id]);

        $action = app(DeletePostMediaAction::class);
        // Try to delete post2's media using post1's ID (should do nothing)
        $action->execute($post1->id, $media2->id);

        // Both media should still exist because media2 doesn't belong to post1
        $this->assertDatabaseHas('media', ['id' => $media1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id]);
        
        @unlink($tempFile1);
        @unlink($tempFile2);
    }
}
