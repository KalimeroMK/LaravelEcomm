<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Illuminate\Support\Facades\Auth;
use Modules\Cart\Actions\GetUserCartAction;
use Modules\Cart\Models\Cart;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\User\Database\Factories\UserFactory;
use Tests\Unit\Actions\ActionTestCase;

class GetUserCartActionTest extends ActionTestCase
{
    public function testExecuteReturnsUserCartItems(): void
    {
        $user = UserFactory::new()->create();
        $product = ProductFactory::new()->create();
        
        Cart::factory()->count(3)->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => null,
        ]);

        Auth::login($user);
        
        $action = app(GetUserCartAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        $this->assertTrue($result->every(fn ($item) => $item->user_id === $user->id));
    }

    public function testExecuteDoesNotReturnOrderedItems(): void
    {
        $user = UserFactory::new()->create();
        
        // Cart item with order
        Cart::factory()->create([
            'user_id' => $user->id,
            'order_id' => 1,
        ]);
        
        // Cart item without order
        Cart::factory()->create([
            'user_id' => $user->id,
            'order_id' => null,
        ]);

        Auth::login($user);
        
        $action = app(GetUserCartAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertNull($result->first()->order_id);
    }

    public function testExecuteReturnsEmptyCollectionForGuest(): void
    {
        $action = app(GetUserCartAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
