<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Banner;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class UpdateBannerActionTest extends ActionTestCase
{
    public function test_execute_updates_banner_with_dto(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Original Title',
            'slug' => 'original-slug',
            'description' => 'Original description.',
            'status' => 'inactive',
        ]);

        $repository = new BannerRepository();
        $action = new UpdateBannerAction($repository);

        $dto = new BannerDTO(
            id: $banner->id,
            title: 'Updated Title',
            slug: 'updated-slug',
            description: 'Updated description.',
            status: 'active',
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals('Updated Title', $result->title);
        $this->assertEquals('updated-slug', $result->slug);
        $this->assertEquals('Updated description.', $result->description);
        $this->assertEquals('active', $result->status);
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'title' => 'Updated Title',
            'slug' => 'updated-slug',
            'status' => 'active',
        ]);
    }

    public function test_execute_preserves_slug_when_not_provided(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Original Title',
            'slug' => 'original-slug',
        ]);

        $repository = new BannerRepository();
        $action = new UpdateBannerAction($repository);

        $dto = new BannerDTO(
            id: $banner->id,
            title: 'Updated Title',
            slug: null,
            description: 'Updated description.',
            status: 'active',
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Updated Title', $result->title);
        $this->assertEquals('original-slug', $result->slug); // preserved
    }

    public function test_execute_updates_banner_categories(): void
    {
        // Arrange
        $banner = Banner::factory()->create();
        $originalCategories = Category::factory()->count(2)->create();
        $banner->categories()->attach($originalCategories);

        $newCategories = Category::factory()->count(3)->create();

        $repository = new BannerRepository();
        $action = new UpdateBannerAction($repository);

        $dto = new BannerDTO(
            id: $banner->id,
            title: $banner->title,
            slug: $banner->slug,
            description: $banner->description,
            status: $banner->status,
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: $newCategories->pluck('id')->toArray(),
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertCount(3, $result->categories);
        $this->assertDatabaseMissing('banner_category', [
            'banner_id' => $banner->id,
            'category_id' => $originalCategories->first()->id,
        ]);
        $this->assertDatabaseHas('banner_category', [
            'banner_id' => $banner->id,
            'category_id' => $newCategories->first()->id,
        ]);
    }

    public function test_execute_updates_advanced_settings(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'max_clicks' => 100,
            'max_impressions' => 1000,
        ]);

        $repository = new BannerRepository();
        $action = new UpdateBannerAction($repository);

        $dto = new BannerDTO(
            id: $banner->id,
            title: $banner->title,
            slug: $banner->slug,
            description: $banner->description,
            status: $banner->status,
            active_from: '2025-06-01 00:00:00',
            active_to: '2025-12-31 23:59:59',
            max_clicks: 500,
            max_impressions: 5000,
            categories: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals(500, $result->max_clicks);
        $this->assertEquals(5000, $result->max_impressions);
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'max_clicks' => 500,
            'max_impressions' => 5000,
        ]);
    }

    public function test_execute_throws_exception_for_nonexistent_banner(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new UpdateBannerAction($repository);

        $dto = new BannerDTO(
            id: 999999,
            title: 'Nonexistent Banner',
            slug: 'nonexistent',
            description: 'This banner does not exist.',
            status: 'active',
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: [],
        );

        // Assert & Act
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }
}
