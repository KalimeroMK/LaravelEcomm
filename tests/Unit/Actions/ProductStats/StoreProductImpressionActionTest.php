<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProductStats;

use Illuminate\Support\Facades\Event;
use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\StoreProductImpressionAction;
use Modules\ProductStats\Events\ProductImpressionRecorded;
use Modules\ProductStats\Models\ProductImpression;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class StoreProductImpressionActionTest extends ActionTestCase
{
    public function testExecuteCreatesImpressionRecords(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, '127.0.0.1');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProductImpression::class, $result->first());
        $this->assertDatabaseHas('product_impressions', [
            'product_id' => $product->id,
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function testExecuteCreatesMultipleImpressions(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product1->id, $product2->id, $product3->id], null, '127.0.0.1');

        $this->assertCount(3, $result);
        $this->assertEquals(3, ProductImpression::count());
    }

    public function testExecuteStoresCorrectProductIds(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, '127.0.0.1');

        $this->assertEquals($product->id, $result->first()->product_id);
    }

    public function testExecuteStoresCorrectIpAddress(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, '192.168.1.1');

        $this->assertEquals('192.168.1.1', $result->first()->ip_address);
    }

    public function testExecuteStoresUserIdWhenProvided(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], $user->id, '127.0.0.1');

        $this->assertEquals($user->id, $result->first()->user_id);
        $this->assertDatabaseHas('product_impressions', [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }

    public function testExecuteAllowsNullUserId(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, '127.0.0.1');

        $this->assertNull($result->first()->user_id);
    }

    public function testExecuteDispatchesProductImpressionRecordedEvent(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $action->execute([$product->id], null, '127.0.0.1');

        Event::assertDispatched(ProductImpressionRecorded::class);
    }

    public function testExecuteDispatchesEventForEachProduct(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $action->execute([$product1->id, $product2->id], null, '127.0.0.1');

        Event::assertDispatched(ProductImpressionRecorded::class, 2);
    }

    public function testExecuteReturnsEmptyCollectionForEmptyProductIds(): void
    {
        Event::fake([ProductImpressionRecorded::class]);

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([], null, '127.0.0.1');

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsCollectionInstance(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, '127.0.0.1');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testExecuteCreatesImpressionWithIpv6Address(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        $action = app(StoreProductImpressionAction::class);
        $result = $action->execute([$product->id], null, $ipv6);

        $this->assertEquals($ipv6, $result->first()->ip_address);
    }

    public function testExecuteCreatesSeparateRecordsForSameProductMultipleTimes(): void
    {
        Event::fake([ProductImpressionRecorded::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductImpressionAction::class);
        $action->execute([$product->id, $product->id], null, '127.0.0.1');

        $this->assertEquals(2, ProductImpression::where('product_id', $product->id)->count());
    }
}
