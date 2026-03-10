<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Modules\Cart\Actions\UpdateCartAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateCartActionTest extends ActionTestCase
{
    public function testExecuteUpdatesCartQuantitySuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
            'order_id' => null,
        ]);

        $dto = new CartDTO(
            id: $cart->id,
            product_id: $product->id,
            quantity: 5,
            user_id: $user->id,
            price: 100,
            session_id: 'test-session',
            amount: 500, // 5 * 100
            order_id: null
        );

        $action = app(UpdateCartAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(5, $result->quantity);
        $this->assertEquals(500, $result->amount);
        
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 5,
            'amount' => 500,
        ]);
    }

    public function testExecuteUpdatesCartPriceAndRecalculatesAmount(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
            'amount' => 200,
            'order_id' => null,
        ]);

        $dto = new CartDTO(
            id: $cart->id,
            product_id: $product->id,
            quantity: 2,
            user_id: $user->id,
            price: 150, // Updated price
            session_id: 'test-session',
            amount: 300, // 2 * 150
            order_id: null
        );

        $action = app(UpdateCartAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(150, $result->price);
        $this->assertEquals(300, $result->amount);
    }

    public function testExecuteUpdatesCartOrderId(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        // Create an order first
        $orderId = \DB::table('orders')->insertGetId([
            'order_number' => 'ORD-TEST-002',
            'user_id' => $user->id,
            'sub_total' => 200,
            'total_amount' => 200,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
            'amount' => 200,
            'order_id' => null,
        ]);

        $dto = new CartDTO(
            id: $cart->id,
            product_id: $product->id,
            quantity: 2,
            user_id: $user->id,
            price: 100,
            session_id: 'test-session',
            amount: 200,
            order_id: $orderId
        );

        $action = app(UpdateCartAction::class);
        $result = $action->execute($dto);

        $this->assertEquals($orderId, $result->order_id);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'order_id' => $orderId,
        ]);
    }

    public function testExecuteCalculatesAmountWhenNotProvided(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
            'order_id' => null,
        ]);

        // DTO without amount - should be calculated as price * quantity (50 * 4 = 200)
        $dto = new CartDTO(
            id: $cart->id,
            product_id: $product->id,
            quantity: 4,
            user_id: $user->id,
            price: 50,
            session_id: 'test-session',
            amount: null, // Not provided - should be calculated
            order_id: null
        );

        $action = app(UpdateCartAction::class);
        $result = $action->execute($dto);

        // The action should calculate amount as price * quantity
        $this->assertEquals(200, $result->amount); // 50 * 4
    }
}
