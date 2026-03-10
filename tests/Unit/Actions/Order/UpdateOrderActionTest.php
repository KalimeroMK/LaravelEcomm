<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Order\Actions\UpdateOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateOrderActionTest extends ActionTestCase
{
    public function testExecuteUpdatesOrderSuccessfully(): void
    {
        $user = User::factory()->create();
        
        // Create order in database
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dto = new OrderDTO(
            id: 1,
            order_number: null,
            user_id: null,
            sub_total: null,
            shipping_id: null,
            total_amount: null,
            quantity: null,
            payment_method: 'paypal',
            payment_status: 'paid',
            status: 'processing',
            payer_id: null,
            transaction_reference: 'TXN-12345',
        );

        $action = app(UpdateOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals('paypal', $result->payment_method);
        $this->assertEquals('paid', $result->payment_status);
        $this->assertEquals('processing', $result->status);
        $this->assertEquals('TXN-12345', $result->transaction_reference);
        
        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'status' => 'processing',
            'transaction_reference' => 'TXN-12345',
        ]);
    }

    public function testExecuteUpdatesOrderStatusOnly(): void
    {
        $user = User::factory()->create();
        
        // Create order in database
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dto = new OrderDTO(
            id: 1,
            order_number: null,
            user_id: null,
            sub_total: null,
            shipping_id: null,
            total_amount: null,
            quantity: null,
            payment_method: null,
            payment_status: null,
            status: 'shipped',
            payer_id: null,
            transaction_reference: null,
        );

        $action = app(UpdateOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals('shipped', $result->status);
        // Other fields should remain unchanged
        $this->assertEquals('cash', $result->payment_method);
        $this->assertEquals('pending', $result->payment_status);
    }

    public function testExecuteUpdatesOrderNumber(): void
    {
        $user = User::factory()->create();
        
        // Create order in database
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dto = new OrderDTO(
            id: 1,
            order_number: 'ORD-UPDATED',
            user_id: null,
            sub_total: null,
            shipping_id: null,
            total_amount: null,
            quantity: null,
            payment_method: null,
            payment_status: null,
            status: null,
            payer_id: null,
            transaction_reference: null,
        );

        $action = app(UpdateOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals('ORD-UPDATED', $result->order_number);
    }

    public function testExecuteUpdatesTotalAmountAndQuantity(): void
    {
        $user = User::factory()->create();
        
        // Create order in database
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dto = new OrderDTO(
            id: 1,
            order_number: null,
            user_id: null,
            sub_total: 200,
            shipping_id: null,
            total_amount: 220,
            quantity: 2,
            payment_method: null,
            payment_status: null,
            status: null,
            payer_id: null,
            transaction_reference: null,
        );

        $action = app(UpdateOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(200, $result->sub_total);
        $this->assertEquals(220, $result->total_amount);
        $this->assertEquals(2, $result->quantity);
    }

    public function testExecuteUpdatesPayerId(): void
    {
        $user = User::factory()->create();
        $payer = User::factory()->create();
        
        // Create order in database
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'paypal',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dto = new OrderDTO(
            id: 1,
            order_number: null,
            user_id: null,
            sub_total: null,
            shipping_id: null,
            total_amount: null,
            quantity: null,
            payment_method: null,
            payment_status: 'paid',
            status: 'completed',
            payer_id: $payer->id,
            transaction_reference: 'PAYPAL-123',
        );

        $action = app(UpdateOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($payer->id, $result->payer_id);
        $this->assertEquals('paid', $result->payment_status);
        $this->assertEquals('completed', $result->status);
        $this->assertEquals('PAYPAL-123', $result->transaction_reference);
    }
}
