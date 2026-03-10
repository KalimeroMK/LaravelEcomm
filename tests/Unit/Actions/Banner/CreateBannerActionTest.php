<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Banner;

use Modules\Banner\Actions\CreateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Category\Models\Category;
use Tests\Unit\Actions\ActionTestCase;

class CreateBannerActionTest extends ActionTestCase
{
    public function test_execute_creates_banner_with_dto(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new CreateBannerAction($repository);

        $dto = new BannerDTO(
            id: null,
            title: 'Test Banner',
            slug: 'test-banner',
            description: 'This is a test banner description.',
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
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals('Test Banner', $result->title);
        $this->assertEquals('test-banner', $result->slug);
        $this->assertEquals('This is a test banner description.', $result->description);
        $this->assertEquals('active', $result->status);
        $this->assertDatabaseHas('banners', [
            'title' => 'Test Banner',
            'slug' => 'test-banner',
            'description' => 'This is a test banner description.',
            'status' => 'active',
        ]);
    }

    public function test_execute_creates_inactive_banner(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new CreateBannerAction($repository);

        $dto = new BannerDTO(
            id: null,
            title: 'Inactive Banner',
            slug: 'inactive-banner',
            description: 'This is an inactive banner.',
            status: 'inactive',
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals('inactive', $result->status);
        $this->assertDatabaseHas('banners', [
            'title' => 'Inactive Banner',
            'status' => 'inactive',
        ]);
    }

    public function test_execute_creates_banner_with_categories(): void
    {
        // Arrange
        $categories = Category::factory()->count(2)->create();
        $repository = new BannerRepository();
        $action = new CreateBannerAction($repository);

        $dto = new BannerDTO(
            id: null,
            title: 'Banner with Categories',
            slug: 'banner-with-categories',
            description: 'This banner has categories.',
            status: 'active',
            active_from: null,
            active_to: null,
            max_clicks: null,
            max_impressions: null,
            categories: $categories->pluck('id')->toArray(),
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals('Banner with Categories', $result->title);
        $this->assertCount(2, $result->categories);
        $this->assertDatabaseHas('banner_category', [
            'banner_id' => $result->id,
            'category_id' => $categories->first()->id,
        ]);
    }

    public function test_execute_creates_banner_with_advanced_settings(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new CreateBannerAction($repository);

        $dto = new BannerDTO(
            id: null,
            title: 'Advanced Banner',
            slug: 'advanced-banner',
            description: 'This banner has advanced settings.',
            status: 'active',
            active_from: '2025-01-01 00:00:00',
            active_to: '2025-12-31 23:59:59',
            max_clicks: 1000,
            max_impressions: 10000,
            categories: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals('Advanced Banner', $result->title);
        $this->assertEquals('active', $result->status);
        $this->assertEquals(1000, $result->max_clicks);
        $this->assertEquals(10000, $result->max_impressions);
        $this->assertDatabaseHas('banners', [
            'title' => 'Advanced Banner',
            'max_clicks' => 1000,
            'max_impressions' => 10000,
        ]);
    }

    public function test_execute_uses_default_status_when_not_provided(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new CreateBannerAction($repository);

        $dto = BannerDTO::fromArray([
            'title' => 'Default Status Banner',
            'slug' => 'default-status-banner',
            'description' => 'This banner uses default status.',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals('inactive', $result->status); // default status
        $this->assertDatabaseHas('banners', [
            'title' => 'Default Status Banner',
            'status' => 'inactive',
        ]);
    }
}
