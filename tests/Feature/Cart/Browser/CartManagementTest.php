<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->brand = Brand::factory()->create();
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);
});

test('user can add product to cart', function () {
    $response = $this->actingAs($this->user)
        ->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);
});

test('user can view cart', function () {
    $response = $this->actingAs($this->user)
        ->get('/cart');

    $response->assertStatus(200);
    $response->assertSee('Shopping Cart');
});

test('user can update cart item quantity', function () {
    $this->actingAs($this->user)
        ->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

    $response = $this->actingAs($this->user)
        ->put('/cart/update', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 3,
    ]);
});

test('user can remove item from cart', function () {
    $this->actingAs($this->user)
        ->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

    $response = $this->actingAs($this->user)
        ->delete('/cart/remove', [
            'product_id' => $this->product->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

test('user can clear entire cart', function () {
    $this->actingAs($this->user)
        ->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

    $response = $this->actingAs($this->user)
        ->delete('/cart/clear');

    $response->assertRedirect();
    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $this->user->id,
    ]);
});
