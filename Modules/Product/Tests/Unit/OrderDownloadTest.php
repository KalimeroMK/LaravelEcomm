<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Models\Order;
use Modules\Product\Models\OrderDownload;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductDownload;
use Modules\Product\Tests\ProductTestCase;
use Modules\User\Models\User;

class OrderDownloadTest extends ProductTestCase
{
    use RefreshDatabase;

    public function test_can_create_order_download(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'downloads_count' => 0,
            'expires_at' => now()->addDays(7),
        ]);
        
        $this->assertDatabaseHas('order_downloads', [
            'id' => $orderDownload->id,
            'order_id' => $order->id,
            'downloads_count' => 0,
        ]);
    }

    public function test_can_download_returns_true_when_valid(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'max_downloads' => 5,
        ]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'downloads_count' => 2,
            'expires_at' => now()->addDays(7),
        ]);
        
        $this->assertTrue($orderDownload->canDownload());
        $this->assertFalse($orderDownload->isExpired());
        $this->assertFalse($orderDownload->isLimitReached());
    }

    public function test_is_expired_returns_true_when_past_expiry(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'expires_at' => now()->subDay(), // Yesterday
        ]);
        
        $this->assertTrue($orderDownload->isExpired());
        $this->assertFalse($orderDownload->canDownload());
    }

    public function test_is_expired_returns_false_when_no_expiry(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'expires_at' => null, // Never expires
        ]);
        
        $this->assertFalse($orderDownload->isExpired());
    }

    public function test_is_limit_reached_returns_true_when_max_reached(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'max_downloads' => 3,
        ]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'downloads_count' => 3, // Max reached
        ]);
        
        $this->assertTrue($orderDownload->isLimitReached());
        $this->assertFalse($orderDownload->canDownload());
    }

    public function test_is_limit_reached_returns_false_when_no_limit(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'max_downloads' => null, // Unlimited
        ]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'downloads_count' => 100, // Many downloads
        ]);
        
        $this->assertFalse($orderDownload->isLimitReached());
    }

    public function test_record_download_increments_count(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'downloads_count' => 0,
        ]);
        
        $orderDownload->recordDownload();
        
        $this->assertEquals(1, $orderDownload->fresh()->downloads_count);
        $this->assertNotNull($orderDownload->fresh()->last_downloaded_at);
    }

    public function test_for_user_scope(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $order1 = Order::factory()->create(['user_id' => $user1->id]);
        $order2 = Order::factory()->create(['user_id' => $user2->id]);
        
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        OrderDownload::create([
            'order_id' => $order1->id,
            'product_download_id' => $download->id,
            'user_id' => $user1->id,
        ]);
        
        OrderDownload::create([
            'order_id' => $order2->id,
            'product_download_id' => $download->id,
            'user_id' => $user2->id,
        ]);
        
        $this->assertEquals(1, OrderDownload::forUser($user1->id)->count());
        $this->assertEquals(1, OrderDownload::forUser($user2->id)->count());
    }

    public function test_valid_scope(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        // Valid - not expired
        OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'expires_at' => now()->addDay(),
        ]);
        
        // Valid - never expires
        OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'expires_at' => null,
        ]);
        
        // Invalid - expired
        OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);
        
        $this->assertEquals(2, OrderDownload::valid()->count());
    }

    public function test_relations(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $download = ProductDownload::factory()->create(['product_id' => $product->id]);
        
        $orderDownload = OrderDownload::create([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(Order::class, $orderDownload->order);
        $this->assertInstanceOf(ProductDownload::class, $orderDownload->productDownload);
        $this->assertInstanceOf(User::class, $orderDownload->user);
    }
}
