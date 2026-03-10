<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Modules\Cart\Actions\CreateCartAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreateCartActionTest extends ActionTestCase
{
    public function testExecuteCreatesCartItemSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $dto = new CartDTO(
            id: null,
            product_id: $product->id,
            quantity: 2,
            user_id: $user->id,
            price: $product->price,
            session_id: 'test-session',
            amount: $product->price * 2,
            order_id: null
        );

        $action = app(CreateCartAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(2, $result->quantity);
        $this->assertEquals(200, $result->amount); // 2 * 100
    }

    public function testExecuteCreatesNewCartItemWhenDifferentProduct(): void
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);
        
        // Create existing cart item for product1
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 100,
            'order_id' => null,
        ]);

        // Add product2 to cart
        $dto = new CartDTO(
            id: null,
            product_id: $product2->id,
            quantity: 2,
            user_id: $user->id,
            price: $product2->price,
            session_id: 'test-session',
            amount: $product2->price * 2,
            order_id: null
        );

        $action = app(CreateCartAction::class);
        $result = $action->execute($dto);

        // Should create a new cart item for product2
        $this->assertEquals($product2->id, $result->product_id);
        $this->assertEquals(2, $result->quantity);
        $this->assertEquals(100, $result->amount); // 2 * 50
        
        // User should now have 2 cart items
        $this->assertEquals(2, Cart::where('user_id', $user->id)->count());
    }
}
