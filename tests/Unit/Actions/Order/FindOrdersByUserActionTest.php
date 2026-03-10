<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindOrdersByUserActionTest extends ActionTestCase
{
    public function testExecuteReturnsUserOrders(): void
    {
        $user = User::factory()->create();
        
        // Create orders using raw insert to bypass FK constraints
        \DB::table('orders')->insert([
            [
                'order_number' => 'ORD-001',
                'user_id' => $user->id,
                'sub_total' => 100,
                'total_amount' => 110,
                'quantity' => 1,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => 'ORD-002',
                'user_id' => $user->id,
                'sub_total' => 200,
                'total_amount' => 220,
                'quantity' => 2,
                'payment_method' => 'card',
                'payment_status' => 'pending',
                'status' => 'processing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $action = app(FindOrdersByUserAction::class);
        $result = $action->execute($user->id);

        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn ($order) => $order->user_id === $user->id));
    }

    public function testExecuteReturnsEmptyCollectionForNonExistentUser(): void
    {
        $action = app(FindOrdersByUserAction::class);
        $result = $action->execute(99999);

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
