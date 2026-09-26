<?php

declare(strict_types=1);

namespace Modules\Cart\Tests\Feature;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Listeners\MergeGuestCart;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class GuestCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock' => 5]);

        $response = $this->get('/add-to-cart/'.$product->slug);

        $response->assertRedirect();
        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'user_id' => null,
        ]);
    }

    public function test_guest_cart_is_scoped_to_session(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock' => 5]);

        Cart::create([
            'product_id' => $product->id,
            'user_id' => null,
            'session_id' => 'someone-elses-session',
            'price' => $product->price,
            'amount' => $product->price,
            'quantity' => 1,
        ]);

        $response = $this->get('/cart-list');

        $response->assertStatus(200);
        // The other visitor's row must not leak into this session's cart page.
        $response->assertDontSee('cart-delete/'.Cart::first()->id);
    }

    public function test_guest_cannot_delete_another_visitors_cart_row(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock' => 5]);

        $row = Cart::create([
            'product_id' => $product->id,
            'user_id' => null,
            'session_id' => 'someone-elses-session',
            'price' => $product->price,
            'amount' => $product->price,
            'quantity' => 1,
        ]);

        $this->get('/cart-delete/'.$row->id)->assertStatus(403);
        $this->assertDatabaseHas('carts', ['id' => $row->id]);
    }

    public function test_guest_cart_merges_into_account_on_login(): void
    {
        $user = User::factory()->create();
        $productA = Product::factory()->create(['status' => 'active', 'stock' => 5, 'price' => 10]);
        $productB = Product::factory()->create(['status' => 'active', 'stock' => 5, 'price' => 20]);

        // The user already has product A in their account cart.
        Cart::create([
            'product_id' => $productA->id,
            'user_id' => $user->id,
            'session_id' => null,
            'price' => 10,
            'amount' => 10,
            'quantity' => 1,
        ]);

        // As a guest they added product A again plus product B.
        session()->start();
        $sessionId = session()->getId();

        Cart::create([
            'product_id' => $productA->id,
            'user_id' => null,
            'session_id' => $sessionId,
            'price' => 10,
            'amount' => 10,
            'quantity' => 2,
        ]);
        Cart::create([
            'product_id' => $productB->id,
            'user_id' => null,
            'session_id' => $sessionId,
            'price' => 20,
            'amount' => 20,
            'quantity' => 1,
        ]);

        (new MergeGuestCart)->handle(new Login('web', $user, false));

        // Product A merged into the existing account line (1 + 2 = 3).
        $this->assertDatabaseHas('carts', [
            'product_id' => $productA->id,
            'user_id' => $user->id,
            'quantity' => 3,
            'amount' => 30,
        ]);

        // Product B was claimed by the account.
        $this->assertDatabaseHas('carts', [
            'product_id' => $productB->id,
            'user_id' => $user->id,
            'quantity' => 1,
        ]);

        // No orphaned guest rows remain for this session.
        $this->assertDatabaseMissing('carts', [
            'user_id' => null,
            'session_id' => $sessionId,
        ]);
    }
}
