<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Banner;

use Modules\Banner\Actions\DeleteBannerAction;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteBannerActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_banner(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Banner to Delete',
            'slug' => 'banner-to-delete',
        ]);

        $repository = new BannerRepository();
        $action = new DeleteBannerAction($repository);

        // Act
        $action->execute($banner->id);

        // Assert
        $this->assertDatabaseMissing('banners', [
            'id' => $banner->id,
            'title' => 'Banner to Delete',
        ]);
    }

    public function test_execute_deletes_banner_and_verifies_count(): void
    {
        // Arrange
        $banner1 = Banner::factory()->create();
        $banner2 = Banner::factory()->create();
        $banner3 = Banner::factory()->create();

        $repository = new BannerRepository();
        $action = new DeleteBannerAction($repository);

        // Act
        $action->execute($banner2->id);

        // Assert
        $this->assertDatabaseHas('banners', ['id' => $banner1->id]);
        $this->assertDatabaseMissing('banners', ['id' => $banner2->id]);
        $this->assertDatabaseHas('banners', ['id' => $banner3->id]);
        $this->assertEquals(2, Banner::count());
    }

    public function test_execute_deletes_banner_with_categories(): void
    {
        // Arrange
        $banner = Banner::factory()->create();
        $categories = \Modules\Category\Models\Category::factory()->count(2)->create();
        $banner->categories()->attach($categories);

        $repository = new BannerRepository();
        $action = new DeleteBannerAction($repository);

        // Act
        $action->execute($banner->id);

        // Assert
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
        $this->assertDatabaseMissing('banner_category', ['banner_id' => $banner->id]);
        // Categories themselves should still exist
        $this->assertDatabaseHas('categories', ['id' => $categories->first()->id]);
    }
}
