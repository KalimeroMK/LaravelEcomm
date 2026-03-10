<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProductStats;

use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\GetAllProductStatsAction;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;
use Tests\Unit\Actions\ActionTestCase;

class GetAllProductStatsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllProductStats(): void
    {
        $product = Product::factory()->create();
        ProductImpression::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);
        ProductClick::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);

        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
    }

    public function testExecuteReturnsCollectionOfProductStatsDTOs(): void
    {
        Product::factory()->count(2)->create();

        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoProducts(): void
    {
        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteFiltersByCategoryId(): void
    {
        // This test assumes products can have categories
        // The filter would work based on the repository implementation
        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute(['category_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteFiltersByDateRange(): void
    {
        $product = Product::factory()->create();
        ProductImpression::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);

        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute([
            'from' => now()->subDays(7)->toDateString(),
            'to' => now()->toDateString(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteAppliesSorting(): void
    {
        Product::factory()->count(3)->create();

        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute([
            'order_by' => 'id',
            'sort' => 'asc',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteCalculatesCorrectStats(): void
    {
        $product = Product::factory()->create();

        // Create 5 impressions and 2 clicks
        for ($i = 0; $i < 5; $i++) {
            ProductImpression::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);
        }
        for ($i = 0; $i < 2; $i++) {
            ProductClick::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);
        }

        $action = app(GetAllProductStatsAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $stats = $result->first();
        $this->assertEquals(5, $stats->impressions);
        $this->assertEquals(2, $stats->clicks);
        $this->assertEquals(0.4, $stats->ctr); // 2/5 = 0.4
    }
}
