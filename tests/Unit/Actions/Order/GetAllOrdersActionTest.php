<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Repository\OrderRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllOrdersActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllOrders(): void
    {
        $items = [
            ['id' => 1, 'order_number' => 'ORD-001'],
            ['id' => 2, 'order_number' => 'ORD-002'],
        ];
        $paginator = new LengthAwarePaginator($items, count($items), 20);

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('paginateAll')
            ->once()
            ->andReturn($paginator);

        $action = new GetAllOrdersAction($repository);
        $result = $action->execute();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(2, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoOrders(): void
    {
        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('paginateAll')
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 20));

        $action = new GetAllOrdersAction($repository);
        $result = $action->execute();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertTrue($result->isEmpty());
    }
}
