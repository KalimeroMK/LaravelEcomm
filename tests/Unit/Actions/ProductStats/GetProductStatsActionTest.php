<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProductStats;

use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\GetProductStatsAction;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;
use Tests\Unit\Actions\ActionTestCase;

class GetProductStatsActionTest extends ActionTestCase
{
    public function testExecuteReturnsStatsArray(): void
    {
        $product = Product::factory()->create();

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('clicks', $result);
        $this->assertArrayHasKey('impressions', $result);
        $this->assertArrayHasKey('ctr', $result);
    }

    public function testExecuteReturnsZeroStatsForNewProduct(): void
    {
        $product = Product::factory()->create();

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(0, $result['clicks']);
        $this->assertEquals(0, $result['impressions']);
        $this->assertEquals(0, $result['ctr']);
    }

    public function testExecuteCountsClicksCorrectly(): void
    {
        $product = Product::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            ProductClick::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(5, $result['clicks']);
    }

    public function testExecuteCountsImpressionsCorrectly(): void
    {
        $product = Product::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            ProductImpression::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(10, $result['impressions']);
    }

    public function testExecuteCalculatesCtrCorrectly(): void
    {
        $product = Product::factory()->create();

        // 5 clicks out of 20 impressions = 0.25 CTR
        for ($i = 0; $i < 20; $i++) {
            ProductImpression::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }
        for ($i = 0; $i < 5; $i++) {
            ProductClick::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(0.25, $result['ctr']);
    }

    public function testExecuteReturnsZeroCtrWhenNoImpressions(): void
    {
        $product = Product::factory()->create();

        // Clicks without impressions
        ProductClick::create([
            'product_id' => $product->id,
            'user_id' => null,
            'ip_address' => '127.0.0.1',
        ]);

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(0, $result['ctr']);
    }

    public function testExecuteOnlyCountsStatsForSpecifiedProduct(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        ProductImpression::create(['product_id' => $product1->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);
        ProductImpression::create(['product_id' => $product2->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);
        ProductClick::create(['product_id' => $product1->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product1->id);

        $this->assertEquals(1, $result['impressions']);
        $this->assertEquals(1, $result['clicks']);
    }

    public function testExecuteRoundsCtrToFourDecimals(): void
    {
        $product = Product::factory()->create();

        // 1 click out of 3 impressions = 0.3333...
        for ($i = 0; $i < 3; $i++) {
            ProductImpression::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }
        ProductClick::create([
            'product_id' => $product->id,
            'user_id' => null,
            'ip_address' => '127.0.0.1',
        ]);

        $action = app(GetProductStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertEquals(0.3333, $result['ctr']);
    }
}
