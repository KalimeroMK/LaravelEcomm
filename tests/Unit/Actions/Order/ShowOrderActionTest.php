<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Illuminate\Database\Eloquent\Model;
use Modules\Order\Actions\ShowOrderAction;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;
use Tests\Unit\Actions\ActionTestCase;

class ShowOrderActionTest extends ActionTestCase
{
    public function testExecuteReturnsOrderModel(): void
    {
        $order = \Mockery::mock(Order::class)->makePartial();
        $order->shouldReceive('offsetGet')
            ->with('id')
            ->andReturn(1);
        $order->shouldReceive('offsetGet')
            ->with('order_number')
            ->andReturn('ORD-001');
        $order->id = 1;
        $order->order_number = 'ORD-001';

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($order);

        $action = new ShowOrderAction($repository);
        $result = $action->execute(1);

        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals('ORD-001', $result->order_number);
    }

    public function testExecuteReturnsOrderWithDifferentId(): void
    {
        $order = \Mockery::mock(Order::class)->makePartial();
        $order->id = 999;
        $order->order_number = 'ORD-999';

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn($order);

        $action = new ShowOrderAction($repository);
        $result = $action->execute(999);

        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals(999, $result->id);
        $this->assertEquals('ORD-999', $result->order_number);
    }
}
