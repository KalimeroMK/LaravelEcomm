<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Illuminate\Http\Request;
use Modules\Cart\Actions\UpdateCartItemsAction;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateCartItemsActionTest extends ActionTestCase
{
    public function testExecuteUpdatesMultipleCartItems(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        
        $cart1 = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
            'order_id' => null,
        ]);
        
        $cart2 = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
            'amount' => 200,
            'order_id' => null,
        ]);

        $request = new Request([
            'quantity' => [3, 5], // New quantities
            'qty_id' => [$cart1->id, $cart2->id], // Cart item IDs
        ]);

        $action = app(UpdateCartItemsAction::class);
        $action->execute($request);

        // Verify cart items were updated
        $this->assertDatabaseHas('carts', [
            'id' => $cart1->id,
            'quantity' => 3,
            'amount' => 300, // 3 * 100
        ]);
        
        $this->assertDatabaseHas('carts', [
            'id' => $cart2->id,
            'quantity' => 5,
            'amount' => 500, // 5 * 100
        ]);
    }

    public function testExecuteThrowsExceptionForNonExistentCartItem(): void
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

        $nonExistentId = 99999;

        $request = new Request([
            'quantity' => [3, 5],
            'qty_id' => [$cart->id, $nonExistentId], // One valid, one non-existent
        ]);

        $action = app(UpdateCartItemsAction::class);
        
        // Repository throws exception for non-existent cart
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($request);
    }

    public function testExecuteDoesNothingWhenQuantityNotProvided(): void
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

        $request = new Request([
            'qty_id' => [$cart->id], // Only qty_id, no quantity
        ]);

        $action = app(UpdateCartItemsAction::class);
        $action->execute($request);

        // Cart should remain unchanged
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 2,
            'amount' => 200,
        ]);
    }

    public function testExecuteDoesNothingWhenQtyIdNotProvided(): void
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

        $request = new Request([
            'quantity' => [5], // Only quantity, no qty_id
        ]);

        $action = app(UpdateCartItemsAction::class);
        $action->execute($request);

        // Cart should remain unchanged
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 2,
            'amount' => 200,
        ]);
    }

    public function testExecuteRecalculatesAmountBasedOnNewQuantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50,
            'amount' => 100,
            'order_id' => null,
        ]);

        $request = new Request([
            'quantity' => [10],
            'qty_id' => [$cart->id],
        ]);

        $action = app(UpdateCartItemsAction::class);
        $action->execute($request);

        // Amount should be recalculated as price * new quantity (50 * 10 = 500)
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 10,
            'amount' => 500,
        ]);
    }
}
