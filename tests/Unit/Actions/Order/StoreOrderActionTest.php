<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class StoreOrderActionTest extends ActionTestCase
{
    public function testExecuteCreatesOrderSuccessfully(): void
    {
        $user = User::factory()->create();
        $shipping = Shipping::factory()->create();

        $dto = new OrderDTO(
            id: null,
            order_number: null,
            user_id: $user->id,
            sub_total: 200,
            shipping_id: $shipping->id,
            total_amount: 210, // sub_total + shipping
            quantity: 2,
            payment_method: 'cash_on_delivery',
            payment_status: 'pending',
            status: 'pending',
            payer_id: null,
            transaction_reference: null,
            first_name: 'John',
            last_name: 'Doe',
            email: 'john@example.com',
            phone: '1234567890',
            country: 'USA',
            city: 'New York',
            state: null,
            address1: 'Test Street 123',
            address2: null,
            post_code: '10001',
        );

        $action = app(StoreOrderAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals(210, $result->total_amount);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    public function testExecuteGeneratesOrderNumber(): void
    {
        $user = User::factory()->create();
        $shipping = Shipping::factory()->create();

        $dto = new OrderDTO(
            id: null,
            order_number: null,
            user_id: $user->id,
            sub_total: 50,
            shipping_id: $shipping->id,
            total_amount: 60,
            quantity: 1,
            payment_method: 'cash_on_delivery',
            payment_status: 'pending',
            status: 'pending',
            payer_id: null,
            transaction_reference: null,
            first_name: 'John',
            last_name: 'Doe',
            email: 'john@example.com',
            phone: '1234567890',
            country: 'USA',
            city: 'New York',
            state: null,
            address1: 'Test Street 123',
            address2: null,
            post_code: '10001',
        );

        $action = app(StoreOrderAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->order_number);
        $this->assertStringStartsWith('ORD-', $result->order_number);
    }

    public function testExecuteUsesProvidedOrderNumber(): void
    {
        $user = User::factory()->create();
        $shipping = Shipping::factory()->create();

        $dto = new OrderDTO(
            id: null,
            order_number: 'CUSTOM-123',
            user_id: $user->id,
            sub_total: 50,
            shipping_id: $shipping->id,
            total_amount: 60,
            quantity: 1,
            payment_method: 'cash_on_delivery',
            payment_status: 'pending',
            status: 'pending',
            payer_id: null,
            transaction_reference: null,
            first_name: 'John',
            last_name: 'Doe',
            email: 'john@example.com',
            phone: '1234567890',
            country: 'USA',
            city: 'New York',
            state: null,
            address1: 'Test Street 123',
            address2: null,
            post_code: '10001',
        );

        $action = app(StoreOrderAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('CUSTOM-123', $result->order_number);
    }
}
