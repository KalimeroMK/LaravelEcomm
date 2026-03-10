<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Cart;

use Illuminate\Support\Facades\Auth;
use Modules\Cart\Actions\GetUserCartAction;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetUserCartActionTest extends ActionTestCase
{
    public function testExecuteReturnsUserCartItems(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        // Create cart items using raw insert
        for ($i = 0; $i < 3; $i++) {
            \DB::table('carts')->insert([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => 100,
                'quantity' => 1,
                'amount' => 100,
                'order_id' => null,
                'session_id' => 'test-session-' . $i,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Auth::login($user);
        
        $action = app(GetUserCartAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        $this->assertTrue($result->every(fn ($item) => $item->user_id === $user->id));
    }

    public function testExecuteDoesNotReturnOrderedItems(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        // First create a real order to satisfy FK
        $orderId = \DB::table('orders')->insertGetId([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 100,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Cart item with the real order_id
        \DB::table('carts')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100,
            'quantity' => 1,
            'amount' => 100,
            'order_id' => $orderId,
            'session_id' => 'test-session-1',
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Cart item without order
        \DB::table('carts')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 50,
            'quantity' => 2,
            'amount' => 100,
            'order_id' => null,
            'session_id' => 'test-session-2',
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Auth::login($user);
        
        $action = app(GetUserCartAction::class);
        $result = $action->execute();

        // The action should only return items without order_id
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
