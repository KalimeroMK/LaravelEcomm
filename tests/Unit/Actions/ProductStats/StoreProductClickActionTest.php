<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProductStats;

use Illuminate\Support\Facades\Event;
use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\StoreProductClickAction;
use Modules\ProductStats\Events\ProductClicked;
use Modules\ProductStats\Models\ProductClick;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class StoreProductClickActionTest extends ActionTestCase
{
    public function testExecuteCreatesClickRecord(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, null, '127.0.0.1');

        $this->assertInstanceOf(ProductClick::class, $result);
        $this->assertDatabaseHas('product_clicks', [
            'product_id' => $product->id,
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function testExecuteStoresCorrectProductId(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, null, '127.0.0.1');

        $this->assertEquals($product->id, $result->product_id);
    }

    public function testExecuteStoresCorrectIpAddress(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, null, '192.168.1.1');

        $this->assertEquals('192.168.1.1', $result->ip_address);
    }

    public function testExecuteStoresUserIdWhenProvided(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, $user->id, '127.0.0.1');

        $this->assertEquals($user->id, $result->user_id);
        $this->assertDatabaseHas('product_clicks', [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }

    public function testExecuteAllowsNullUserId(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, null, '127.0.0.1');

        $this->assertNull($result->user_id);
    }

    public function testExecuteDispatchesProductClickedEvent(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $action->execute($product->id, null, '127.0.0.1');

        Event::assertDispatched(ProductClicked::class, function ($event) use ($product) {
            return $event->click->product_id === $product->id;
        });
    }

    public function testExecuteCreatesMultipleClicks(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $action->execute($product->id, null, '127.0.0.1');
        $action->execute($product->id, null, '127.0.0.1');
        $action->execute($product->id, null, '127.0.0.1');

        $this->assertEquals(3, ProductClick::where('product_id', $product->id)->count());
    }

    public function testExecuteCreatesClicksForDifferentProducts(): void
    {
        Event::fake([ProductClicked::class]);
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $action = app(StoreProductClickAction::class);
        $action->execute($product1->id, null, '127.0.0.1');
        $action->execute($product2->id, null, '127.0.0.1');

        $this->assertEquals(1, ProductClick::where('product_id', $product1->id)->count());
        $this->assertEquals(1, ProductClick::where('product_id', $product2->id)->count());
    }

    public function testExecuteCreatesClickWithIpv6Address(): void
    {
        Event::fake([ProductClicked::class]);
        $product = Product::factory()->create();
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        $action = app(StoreProductClickAction::class);
        $result = $action->execute($product->id, null, $ipv6);

        $this->assertEquals($ipv6, $result->ip_address);
    }
}
