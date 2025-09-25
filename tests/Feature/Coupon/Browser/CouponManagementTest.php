<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Coupon\Models\Coupon;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->user = User::factory()->create();
});

test('admin can view coupons list', function () {
    Coupon::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/coupons');

    $response->assertStatus(200);
    $response->assertSee('Coupons');
});

test('admin can create coupon', function () {
    $couponData = [
        'code' => 'TEST10',
        'type' => 'percentage',
        'value' => 10,
        'min_amount' => 100,
        'max_uses' => 100,
        'expires_at' => now()->addMonth(),
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/coupons', $couponData);

    $response->assertRedirect();
    $this->assertDatabaseHas('coupons', [
        'code' => 'TEST10',
        'type' => 'percentage',
    ]);
});

test('user can apply valid coupon', function () {
    $coupon = Coupon::factory()->create([
        'code' => 'VALID10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
        'expires_at' => now()->addMonth(),
    ]);

    $response = $this->actingAs($this->user)
        ->post('/coupon/apply', [
            'code' => 'VALID10',
            'amount' => 100,
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'discount',
        'final_amount',
    ]);
});

test('user cannot apply expired coupon', function () {
    $coupon = Coupon::factory()->create([
        'code' => 'EXPIRED10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
        'expires_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->user)
        ->post('/coupon/apply', [
            'code' => 'EXPIRED10',
            'amount' => 100,
        ]);

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'message' => 'Coupon has expired',
    ]);
});

test('user cannot apply invalid coupon', function () {
    $response = $this->actingAs($this->user)
        ->post('/coupon/apply', [
            'code' => 'INVALID',
            'amount' => 100,
        ]);

    $response->assertStatus(404);
    $response->assertJson([
        'success' => false,
        'message' => 'Coupon not found',
    ]);
});

test('admin can deactivate coupon', function () {
    $coupon = Coupon::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/coupons/{$coupon->id}", [
            'is_active' => false,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('coupons', [
        'id' => $coupon->id,
        'is_active' => false,
    ]);
});
