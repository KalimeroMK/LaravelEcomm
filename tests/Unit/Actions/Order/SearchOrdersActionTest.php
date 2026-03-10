<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Order\Actions\SearchOrdersAction;
use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Repository\OrderRepository;
use Tests\Unit\Actions\ActionTestCase;

class SearchOrdersActionTest extends ActionTestCase
{
    public function testExecuteReturnsOrderListDTO(): void
    {
        $paginator = new LengthAwarePaginator(
            collect([
                ['id' => 1, 'order_number' => 'ORD-001'],
                ['id' => 2, 'order_number' => 'ORD-002'],
            ]),
            2,
            20
        );

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('search')
            ->once()
            ->with(['status' => 'pending'])
            ->andReturn($paginator);

        $action = new SearchOrdersAction($repository);
        $result = $action->execute(['status' => 'pending']);

        $this->assertInstanceOf(OrderListDTO::class, $result);
        $this->assertSame($paginator, $result->orders);
    }

    public function testExecuteWithEmptyCriteria(): void
    {
        $paginator = new LengthAwarePaginator(
            collect([]),
            0,
            20
        );

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('search')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $action = new SearchOrdersAction($repository);
        $result = $action->execute([]);

        $this->assertInstanceOf(OrderListDTO::class, $result);
    }

    public function testExecuteWithMultipleCriteria(): void
    {
        $criteria = [
            'status' => 'completed',
            'payment_status' => 'paid',
            'order_number' => 'ORD-001',
        ];

        $paginator = new LengthAwarePaginator(
            collect([
                ['id' => 1, 'order_number' => 'ORD-001', 'status' => 'completed'],
            ]),
            1,
            20
        );

        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('search')
            ->once()
            ->with($criteria)
            ->andReturn($paginator);

        $action = new SearchOrdersAction($repository);
        $result = $action->execute($criteria);

        $this->assertInstanceOf(OrderListDTO::class, $result);
        $this->assertCount(1, $result->orders->items());
    }
}
