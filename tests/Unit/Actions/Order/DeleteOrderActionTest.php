<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Repository\OrderRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteOrderActionTest extends ActionTestCase
{
    public function testExecuteDeletesOrderSuccessfully(): void
    {
        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('destroy')
            ->once()
            ->with(1);

        $action = new DeleteOrderAction($repository);
        $action->execute(1);
    }

    public function testExecuteDeletesOrderWithDifferentId(): void
    {
        $repository = $this->mock(OrderRepository::class);
        $repository->shouldReceive('destroy')
            ->once()
            ->with(999);

        $action = new DeleteOrderAction($repository);
        $action->execute(999);
    }
}
