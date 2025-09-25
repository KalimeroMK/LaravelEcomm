<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\Shipping\Models\ShippingMethod;
use Modules\Shipping\Models\ShippingZone;
use Modules\Order\Models\Order;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->user = User::factory()->create();
});

test('admin can view shipping methods', function () {
    ShippingMethod::factory()->count(5)->create();
    
    $response = $this->actingAs($this->admin)
        ->get('/admin/shipping/methods');
    
    $response->assertStatus(200);
    $response->assertSee('Shipping Methods');
});

test('admin can create shipping method', function () {
    $methodData = [
        'name' => 'Express Shipping',
        'description' => 'Fast delivery within 24 hours',
        'cost' => 15.00,
        'free_threshold' => 100.00,
        'is_active' => true
    ];
    
    $response = $this->actingAs($this->admin)
        ->post('/admin/shipping/methods', $methodData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('shipping_methods', [
        'name' => 'Express Shipping',
        'cost' => 15.00
    ]);
});

test('admin can update shipping method', function () {
    $method = ShippingMethod::factory()->create([
        'cost' => 10.00
    ]);
    
    $response = $this->actingAs($this->admin)
        ->put("/admin/shipping/methods/{$method->id}", [
            'cost' => 12.00,
            'free_threshold' => 150.00
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('shipping_methods', [
        'id' => $method->id,
        'cost' => 12.00,
        'free_threshold' => 150.00
    ]);
});

test('admin can view shipping zones', function () {
    ShippingZone::factory()->count(3)->create();
    
    $response = $this->actingAs($this->admin)
        ->get('/admin/shipping/zones');
    
    $response->assertStatus(200);
    $response->assertSee('Shipping Zones');
});

test('admin can create shipping zone', function () {
    $zoneData = [
        'name' => 'North America',
        'countries' => ['US', 'CA', 'MX'],
        'is_active' => true
    ];
    
    $response = $this->actingAs($this->admin)
        ->post('/admin/shipping/zones', $zoneData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('shipping_zones', [
        'name' => 'North America'
    ]);
});

test('shipping cost calculation works', function () {
    $method = ShippingMethod::factory()->create([
        'cost' => 10.00,
        'free_threshold' => 100.00
    ]);
    
    // Test with amount below threshold
    $response = $this->post('/api/shipping/calculate', [
        'method_id' => $method->id,
        'amount' => 50.00
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'cost' => 10.00,
        'free_shipping' => false
    ]);
    
    // Test with amount above threshold
    $response = $this->post('/api/shipping/calculate', [
        'method_id' => $method->id,
        'amount' => 150.00
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'cost' => 0.00,
        'free_shipping' => true
    ]);
});

test('user can select shipping method during checkout', function () {
    $method = ShippingMethod::factory()->create([
        'name' => 'Standard Shipping',
        'cost' => 8.00
    ]);
    
    $response = $this->actingAs($this->user)
        ->post('/checkout/shipping', [
            'method_id' => $method->id
        ]);
    
    $response->assertRedirect();
    $this->assertSessionHas('shipping_method_id', $method->id);
});

test('order tracking works', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'tracking_number' => 'TRK123456789',
        'status' => 'shipped'
    ]);
    
    $response = $this->actingAs($this->user)
        ->get("/orders/{$order->id}/track");
    
    $response->assertStatus(200);
    $response->assertSee('TRK123456789');
    $response->assertSee('shipped');
});

test('admin can update order tracking', function () {
    $order = Order::factory()->create([
        'tracking_number' => null,
        'status' => 'processing'
    ]);
    
    $response = $this->actingAs($this->admin)
        ->put("/admin/orders/{$order->id}/tracking", [
            'tracking_number' => 'TRK987654321',
            'status' => 'shipped'
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'tracking_number' => 'TRK987654321',
        'status' => 'shipped'
    ]);
});
