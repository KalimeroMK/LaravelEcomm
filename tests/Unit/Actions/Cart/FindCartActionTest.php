<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Modules\Cart\Actions\FindCartAction;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindCartActionTest extends ActionTestCase
{
    public function testExecuteReturnsCartById(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 100,
            'amount' => 300,
            'order_id' => null,
        ]);

        $action = app(FindCartAction::class);
        $result = $action->execute($cart->id);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals($cart->id, $result->id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(3, $result->quantity);
        $this->assertEquals(300, $result->amount);
    }

    public function testExecuteReturnsCartWithCorrectRelations(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 150]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 150,
            'amount' => 300,
            'order_id' => null,
        ]);

        $action = app(FindCartAction::class);
        $result = $action->execute($cart->id);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals($cart->id, $result->id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
    }

    public function testExecuteReturnsDifferentCartsForDifferentIds(): void
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

        $action = app(FindCartAction::class);
        
        $result1 = $action->execute($cart1->id);
        $result2 = $action->execute($cart2->id);

        $this->assertEquals($cart1->id, $result1->id);
        $this->assertEquals($cart2->id, $result2->id);
        $this->assertNotEquals($result1->id, $result2->id);
        $this->assertEquals(1, $result1->quantity);
        $this->assertEquals(2, $result2->quantity);
    }
}
