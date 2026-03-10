<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Bundle;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Bundle\Actions\DeleteBundleMediaAction;
use Modules\Bundle\Models\Bundle;
use Tests\Unit\Actions\ActionTestCase;

class DeleteBundleMediaActionTest extends ActionTestCase
{
    public function testExecuteDeletesMediaSuccessfully(): void
    {
        $bundle = Bundle::factory()->create();

        // Add media to the bundle
        $tempFile = tempnam(sys_get_temp_dir(), 'test_media');
        file_put_contents($tempFile, 'test content');
        $media = $bundle->addMedia($tempFile)
            ->toMediaCollection('bundle');

        $this->assertDatabaseHas('media', ['id' => $media->id, 'model_id' => $bundle->id]);

        $action = app(DeleteBundleMediaAction::class);
        $action->execute($bundle->id, $media->id);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);

        @unlink($tempFile);
    }

    public function testExecuteThrowsExceptionForNonExistentBundle(): void
    {
        $action = app(DeleteBundleMediaAction::class);

        $this->expectException(ModelNotFoundException::class);

        $action->execute(99999, 1);
    }

    public function testExecuteThrowsExceptionForNonExistentMedia(): void
    {
        $bundle = Bundle::factory()->create();

        $action = app(DeleteBundleMediaAction::class);

        $this->expectException(ModelNotFoundException::class);

        $action->execute($bundle->id, 99999);
    }

    public function testExecuteOnlyDeletesSpecifiedMedia(): void
    {
        $bundle = Bundle::factory()->create();

        // Add two media items
        $tempFile1 = tempnam(sys_get_temp_dir(), 'test_media1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'test_media2');
        file_put_contents($tempFile1, 'test content 1');
        file_put_contents($tempFile2, 'test content 2');

        $media1 = $bundle->addMedia($tempFile1)->toMediaCollection('bundle');
        $media2 = $bundle->addMedia($tempFile2)->toMediaCollection('bundle');

        $this->assertDatabaseHas('media', ['id' => $media1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id]);

        $action = app(DeleteBundleMediaAction::class);
        $action->execute($bundle->id, $media1->id);

        $this->assertDatabaseMissing('media', ['id' => $media1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id]);

        @unlink($tempFile1);
        @unlink($tempFile2);
    }

    public function testExecuteOnlyDeletesMediaBelongingToSpecifiedBundle(): void
    {
        $bundle1 = Bundle::factory()->create();
        $bundle2 = Bundle::factory()->create();

        // Add media to both bundles
        $tempFile1 = tempnam(sys_get_temp_dir(), 'test_media1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'test_media2');
        file_put_contents($tempFile1, 'test content 1');
        file_put_contents($tempFile2, 'test content 2');

        $media1 = $bundle1->addMedia($tempFile1)->toMediaCollection('bundle');
        $media2 = $bundle2->addMedia($tempFile2)->toMediaCollection('bundle');

        $this->assertDatabaseHas('media', ['id' => $media1->id, 'model_id' => $bundle1->id]);
        $this->assertDatabaseHas('media', ['id' => $media2->id, 'model_id' => $bundle2->id]);

        $action = app(DeleteBundleMediaAction::class);

        // Try to delete bundle2's media using bundle1's ID (should throw exception)
        $this->expectException(ModelNotFoundException::class);
        $action->execute($bundle1->id, $media2->id);

        @unlink($tempFile1);
        @unlink($tempFile2);
    }
}
