<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Illuminate\Support\Collection;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Repository\OrderRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllOrdersActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllOrders(): void
    {
        $expectedCollection = new Collection([
            ['id' => 1, 'order_number' => 'ORD-001'],
            ['id' => 2, 'order_number' => 'ORD-002'],
        ]);

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn($expectedCollection);

        $action = new GetAllOrdersAction($repository);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoOrders(): void
    {
        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn(new Collection());

        $action = new GetAllOrdersAction($repository);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }
}
