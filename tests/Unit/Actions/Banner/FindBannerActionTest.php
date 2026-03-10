<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Banner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Banner\Actions\FindBannerAction;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindBannerActionTest extends ActionTestCase
{
    public function test_execute_finds_banner_by_id(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Findable Banner',
            'slug' => 'findable-banner',
            'description' => 'Content to find',
            'status' => 'active',
        ]);

        $repository = new BannerRepository();
        $action = new FindBannerAction($repository);

        // Act
        $result = $action->execute($banner->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals($banner->id, $result->id);
        $this->assertEquals('Findable Banner', $result->title);
        $this->assertEquals('findable-banner', $result->slug);
    }

    public function test_execute_finds_banner_with_all_attributes(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Complete Banner',
            'slug' => 'complete-banner',
            'description' => 'Complete description here',
            'status' => 'active',
            'max_clicks' => 500,
            'max_impressions' => 5000,
        ]);

        $repository = new BannerRepository();
        $action = new FindBannerAction($repository);

        // Act
        $result = $action->execute($banner->id);

        // Assert
        $this->assertEquals('Complete Banner', $result->title);
        $this->assertEquals('complete-banner', $result->slug);
        $this->assertEquals('Complete description here', $result->description);
        $this->assertEquals('active', $result->status);
        $this->assertEquals(500, $result->max_clicks);
        $this->assertEquals(5000, $result->max_impressions);
    }

    public function test_execute_throws_exception_for_nonexistent_banner(): void
    {
        // Arrange
        $repository = new BannerRepository();
        $action = new FindBannerAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }

    public function test_execute_finds_banner_with_categories(): void
    {
        // Arrange
        $banner = Banner::factory()->create([
            'title' => 'Banner with Categories',
        ]);
        $categories = \Modules\Category\Models\Category::factory()->count(2)->create();
        $banner->categories()->attach($categories);

        $repository = new BannerRepository();
        $action = new FindBannerAction($repository);

        // Act
        $result = $action->execute($banner->id);

        // Assert
        $this->assertInstanceOf(Banner::class, $result);
        $this->assertEquals($banner->id, $result->id);
        $this->assertEquals('Banner with Categories', $result->title);
    }
}
