<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Banner\Models\Banner;
use Modules\Category\Models\Category;
use Modules\Front\Actions\GetBannersAction;
use Tests\Unit\Actions\ActionTestCase;

class GetBannersActionTest extends ActionTestCase
{
    public function test_invoke_returns_banners_array(): void
    {
        $action = app(GetBannersAction::class);
        $result = $action();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('banners', $result);
    }

    public function test_invoke_returns_active_banners(): void
    {
        Banner::factory()->count(3)->create(['status' => 'active']);
        Banner::factory()->count(2)->create(['status' => 'inactive']);

        $action = app(GetBannersAction::class);
        $result = $action();

        foreach ($result['banners'] as $banner) {
            $this->assertEquals('active', $banner->status);
        }
    }

    public function test_invoke_with_category_id_filters_banners(): void
    {
        $category = Category::factory()->create(['status' => 'active']);
        $banner = Banner::factory()->create(['status' => 'active']);
        $banner->categories()->attach($category->id);

        Banner::factory()->count(2)->create(['status' => 'active']);

        $action = app(GetBannersAction::class);
        $result = $action($category->id);

        $this->assertCount(1, $result['banners']);
        $this->assertEquals($banner->id, $result['banners']->first()->id);
    }

    public function test_invoke_without_category_id_returns_all_active(): void
    {
        Banner::factory()->count(4)->create(['status' => 'active']);

        $action = app(GetBannersAction::class);
        $result = $action();

        $this->assertGreaterThanOrEqual(4, $result['banners']->count());
    }

    public function test_invoke_returns_empty_when_no_banners(): void
    {
        $action = app(GetBannersAction::class);
        $result = $action();

        $this->assertEmpty($result['banners']);
    }
}
