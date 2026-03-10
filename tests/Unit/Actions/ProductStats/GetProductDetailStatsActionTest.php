<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProductStats;

use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\GetProductDetailStatsAction;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;
use Tests\Unit\Actions\ActionTestCase;

class GetProductDetailStatsActionTest extends ActionTestCase
{
    public function testExecuteReturnsProductDetails(): void
    {
        $product = Product::factory()->create();

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertArrayHasKey('product', $result);
        $this->assertArrayHasKey('impressions', $result);
        $this->assertArrayHasKey('clicks', $result);
        $this->assertArrayHasKey('stats', $result);
    }

    public function testExecuteReturnsCorrectProduct(): void
    {
        $product = Product::factory()->create(['title' => 'Test Product']);

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertInstanceOf(Product::class, $result['product']);
        $this->assertEquals($product->id, $result['product']->id);
        $this->assertEquals('Test Product', $result['product']->title);
    }

    public function testExecuteReturnsImpressionsCollection(): void
    {
        $product = Product::factory()->create();
        ProductImpression::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result['impressions']);
        $this->assertCount(1, $result['impressions']);
    }

    public function testExecuteReturnsClicksCollection(): void
    {
        $product = Product::factory()->create();
        ProductClick::create(['product_id' => $product->id, 'user_id' => null, 'ip_address' => '127.0.0.1']);

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result['clicks']);
        $this->assertCount(1, $result['clicks']);
    }

    public function testExecuteReturnsStatsArray(): void
    {
        $product = Product::factory()->create();

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertIsArray($result['stats']);
        $this->assertArrayHasKey('clicks', $result['stats']);
        $this->assertArrayHasKey('impressions', $result['stats']);
        $this->assertArrayHasKey('ctr', $result['stats']);
    }

    public function testExecuteFiltersByDateRange(): void
    {
        $product = Product::factory()->create();

        // Create old impression
        $oldImpression = ProductImpression::create([
            'product_id' => $product->id,
            'user_id' => null,
            'ip_address' => '127.0.0.1',
        ]);
        $oldImpression->created_at = now()->subDays(10);
        $oldImpression->save();

        // Create recent impression
        ProductImpression::create([
            'product_id' => $product->id,
            'user_id' => null,
            'ip_address' => '127.0.0.1',
        ]);

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute(
            $product->id,
            now()->subDays(5)->toDateString(),
            now()->toDateString()
        );

        $this->assertCount(1, $result['impressions']);
    }

    public function testExecuteThrowsExceptionForNonExistentProduct(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $action = app(GetProductDetailStatsAction::class);
        $action->execute(99999);
    }

    public function testExecuteLimitsResultsTo30(): void
    {
        $product = Product::factory()->create();

        // Create 35 impressions
        for ($i = 0; $i < 35; $i++) {
            ProductImpression::create([
                'product_id' => $product->id,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
            ]);
        }

        $action = app(GetProductDetailStatsAction::class);
        $result = $action->execute($product->id);

        $this->assertLessThanOrEqual(30, $result['impressions']->count());
    }
}
