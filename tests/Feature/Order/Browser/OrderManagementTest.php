<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->brand = Brand::factory()->create();
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'price' => 100.00,
    ]);
});

test('user can create order', function () {
    $orderData = [
        'user_id' => $this->user->id,
        'total_amount' => 100.00,
        'sub_total' => 100.00,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'shipping_address' => 'Test Address',
        'billing_address' => 'Test Address',
    ];

    $response = $this->actingAs($this->user)
        ->post('/orders', $orderData);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'total_amount' => 100.00,
    ]);
});

test('user can view order history', function () {
    Order::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get('/orders');

    $response->assertStatus(200);
    $response->assertSee('Order History');
});

test('user can view order details', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get("/orders/{$order->id}");

    $response->assertStatus(200);
    $response->assertSee($order->id);
});

test('user can track order status', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'processing',
    ]);

    $response = $this->actingAs($this->user)
        ->get("/orders/{$order->id}/track");

    $response->assertStatus(200);
    $response->assertSee('processing');
});

test('admin can update order status', function () {
    $admin = createAdminUser();
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->put("/admin/orders/{$order->id}", [
            'status' => 'shipped',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'shipped',
    ]);
});
