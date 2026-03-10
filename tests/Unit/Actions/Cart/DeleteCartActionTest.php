<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Modules\Cart\Actions\DeleteCartAction;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeleteCartActionTest extends ActionTestCase
{
    public function testExecuteDeletesCartItemSuccessfully(): void
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

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);

        $action = app(DeleteCartAction::class);
        $action->execute($cart->id);

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function testExecuteDeletesMultipleCartItemsIndependently(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
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
            'price' => 50,
            'amount' => 100,
            'order_id' => null,
        ]);

        $this->assertDatabaseHas('carts', ['id' => $cart1->id]);
        $this->assertDatabaseHas('carts', ['id' => $cart2->id]);

        $action = app(DeleteCartAction::class);
        $action->execute($cart1->id);

        // First cart should be deleted
        $this->assertDatabaseMissing('carts', ['id' => $cart1->id]);
        // Second cart should still exist
        $this->assertDatabaseHas('carts', ['id' => $cart2->id]);
    }
}
