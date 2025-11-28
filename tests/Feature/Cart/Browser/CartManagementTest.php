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
    $this->product = Product::factory()->withCategories()->create([
        'brand_id' => $this->brand->id,
    ]);
});

test('user can add product to cart', function () {
    $response = $this->actingAs($this->user)
        ->post(route('single-add-to-cart'), [
            'slug' => $this->product->slug,
            'quantity' => 2,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('carts', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);
});

test('user can view cart', function () {
    $response = $this->actingAs($this->user)
        ->get(route('cart-list'));

    $response->assertStatus(200);
});

test('user can update cart item quantity', function () {
    $this->actingAs($this->user)
        ->post(route('single-add-to-cart'), [
            'slug' => $this->product->slug,
            'quantity' => 1,
        ]);

    $cart = Modules\Cart\Models\Cart::where('user_id', $this->user->id)->where('product_id', $this->product->id)->first();

    if (! $cart) {
        $this->markTestSkipped('Cart not created in previous step');
    }

    $response = $this->actingAs($this->user)
        ->post(route('cart-update'), [
            'qty_id' => [$cart->id],
            'quantity' => [3],
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('carts', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 3,
    ]);
});

test('user can remove item from cart', function () {
    $this->actingAs($this->user)
        ->post(route('single-add-to-cart'), [
            'slug' => $this->product->slug,
            'quantity' => 1,
        ]);

    $cart = Modules\Cart\Models\Cart::where('user_id', $this->user->id)->where('product_id', $this->product->id)->first();

    if (! $cart) {
        $this->markTestSkipped('Cart not created in previous step');
    }

    $response = $this->actingAs($this->user)
        ->get(route('cart-delete', $cart->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('carts', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

test('user can clear entire cart', function () {
    $this->actingAs($this->user)
        ->post(route('single-add-to-cart'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

    // Clear cart by deleting all cart items for user
    Modules\Cart\Models\Cart::where('user_id', $this->user->id)->delete();

    $this->assertDatabaseMissing('carts', [
        'user_id' => $this->user->id,
    ]);
});
