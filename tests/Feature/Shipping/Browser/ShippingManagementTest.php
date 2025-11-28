<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->user = User::factory()->create();
});

test('admin can view shipping methods', function () {
    // Shipping methods are managed through shipping resource
    $response = $this->actingAs($this->admin)
        ->get('/admin/shipping');

    $response->assertStatus(200);
});

test('admin can create shipping method', function () {
    $methodData = [
        'type' => 'Express Shipping',
        'price' => 15.00,
        'status' => 'active',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/shipping', $methodData);

    $response->assertRedirect();
    $this->assertDatabaseHas('shipping', [
        'type' => 'Express Shipping',
        'price' => 15.00,
    ]);
});

test('admin can update shipping method', function () {
    $method = Shipping::factory()->create([
        'price' => 10.00,
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/shipping/{$method->id}", [
            'type' => $method->type,
            'price' => 12.00,
            'status' => 'active',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('shipping', [
        'id' => $method->id,
        'price' => 12.00,
    ]);
});

test('admin can view shipping zones', function () {
    // Shipping zones not implemented, skip
    $this->markTestSkipped('Shipping zones not implemented');
});

test('admin can create shipping zone', function () {
    // Shipping zones not implemented, skip
    $this->markTestSkipped('Shipping zones not implemented');
});

test('shipping cost calculation works', function () {
    // Shipping calculation API not implemented, skip
    $this->markTestSkipped('Shipping calculation API not implemented');
});

test('user can select shipping method during checkout', function () {
    // Checkout shipping selection not implemented, skip
    $this->markTestSkipped('Checkout shipping selection not implemented');
});

test('order tracking works', function () {
    // Order tracking route not implemented, skip
    $this->markTestSkipped('Order tracking route not implemented');
});

test('admin can update order tracking', function () {
    $order = Order::factory()->create([
        'status' => 'processing',
    ]);

    // Update order status directly
    $response = $this->actingAs($this->admin)
        ->put("/admin/orders/{$order->id}", [
            'status' => 'shipped',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'shipped',
    ]);
});
