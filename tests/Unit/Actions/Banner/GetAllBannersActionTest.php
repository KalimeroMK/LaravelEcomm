<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Banner;

use Illuminate\Support\Collection;
use Modules\Banner\Actions\GetAllBannersAction;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllBannersActionTest extends ActionTestCase
{
    public function test_execute_returns_collection(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new GetAllBannersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_execute_returns_empty_collection_when_no_banners(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new GetAllBannersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_execute_returns_all_banners(): void
    {
        // Arrange
        Banner::factory()->count(3)->create();

        $repository = new BannerRepository();
        $action = new GetAllBannersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_execute_returns_banner_instances(): void
    {
        // Arrange
        Banner::factory()->create(['title' => 'Banner One']);
        Banner::factory()->create(['title' => 'Banner Two']);

        $repository = new BannerRepository();
        $action = new GetAllBannersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Banner::class, $result->first());
        $this->assertInstanceOf(Banner::class, $result->last());
    }

    public function test_execute_returns_banners_with_correct_attributes(): void
    {
        // Arrange
        Banner::factory()->create([
            'title' => 'Active Banner',
            'slug' => 'active-banner',
            'status' => 'active',
        ]);
        Banner::factory()->create([
            'title' => 'Inactive Banner',
            'slug' => 'inactive-banner',
            'status' => 'inactive',
        ]);

        $repository = new BannerRepository();
        $action = new GetAllBannersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(2, $result);
        $titles = $result->pluck('title')->toArray();
        $this->assertContains('Active Banner', $titles);
        $this->assertContains('Inactive Banner', $titles);
    }
}
