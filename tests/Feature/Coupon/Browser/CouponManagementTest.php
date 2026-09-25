<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);


function addCartItemFor($user, float $price = 200.00): void
{
    $product = Product::factory()->create(['price' => $price, 'status' => 'active']);

    Cart::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'price' => $price,
        'amount' => $price,
        'quantity' => 1,
    ]);
}

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->user = User::factory()->create();
});

test('admin can view coupons list', function () {
    Coupon::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/coupons');

    $response->assertStatus(200);
});

test('admin can create coupon', function () {
    $couponData = [
        'code' => 'TEST10',
        'type' => 'percent',
        'value' => 10,
        'min_amount' => 100,
        'max_uses' => 100,
        'expires_at' => now()->addMonth(),
        'status' => 'active',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/coupons', $couponData);

    $response->assertRedirect();
    $this->assertDatabaseHas('coupons', [
        'code' => 'TEST10',
        'type' => 'percent',
    ]);
});

test('user can apply valid coupon', function () {
    $coupon = Coupon::factory()->create([
        'code' => 'VALID10',
        'type' => 'percent',
        'value' => 10,
        'status' => 'active',
    ]);

    addCartItemFor($this->user);

    $response = $this->actingAs($this->user)
        ->postJson(route('api.coupons.apply'), [
            'code' => 'VALID10',
        ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'message',
        'code',
        'data' => [
            'success',
            'coupon' => [
                'id',
                'code',
                'value',
            ],
            'discount',
        ],
    ]);
});

test('user cannot apply expired coupon', function () {
    Coupon::factory()->create([
        'code' => 'EXPIRED10',
        'type' => 'percent',
        'value' => 10,
        'status' => 'active',
        'expires_at' => now()->subDay(),
    ]);

    addCartItemFor($this->user);

    $response = $this->actingAs($this->user)
        ->postJson(route('api.coupons.apply'), [
            'code' => 'EXPIRED10',
        ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['message' => 'Coupon has expired']);
});

test('user cannot apply invalid coupon', function () {
    addCartItemFor($this->user);

    $response = $this->actingAs($this->user)
        ->postJson(route('api.coupons.apply'), [
            'code' => 'INVALID',
        ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['message' => 'Invalid coupon code, Please try again']);
});

test('admin can deactivate coupon', function () {
    $coupon = Coupon::factory()->create([
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/coupons/{$coupon->id}", [
            'status' => 'inactive',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('coupons', [
        'id' => $coupon->id,
        'status' => 'inactive',
    ]);
});
