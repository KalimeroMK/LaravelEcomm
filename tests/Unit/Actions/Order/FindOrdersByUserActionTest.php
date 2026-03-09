<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Models\Order;
use Modules\User\Database\Factories\UserFactory;
use Tests\Unit\Actions\ActionTestCase;

class FindOrdersByUserActionTest extends ActionTestCase
{
    public function testExecuteReturnsOrdersForSpecificUser(): void
    {
        $user = UserFactory::new()->create();
        $otherUser = UserFactory::new()->create();
        
        // Create orders for user
        Order::factory()->count(3)->create(['user_id' => $user->id]);
        
        // Create orders for other user
        Order::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $action = app(FindOrdersByUserAction::class);
        $result = $action->execute($user->id);

        $this->assertCount(3, $result->items());
        $this->assertTrue($result->every(fn ($order) => $order->user_id === $user->id));
    }

    public function testExecuteReturnsEmptyPaginationForUserWithNoOrders(): void
    {
        $user = UserFactory::new()->create();

        $action = app(FindOrdersByUserAction::class);
        $result = $action->execute($user->id);

        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }

    public function testExecuteReturnsPaginatedResults(): void
    {
        $user = UserFactory::new()->create();
        Order::factory()->count(25)->create(['user_id' => $user->id]);

        $action = app(FindOrdersByUserAction::class);
        $result = $action->execute($user->id);

        $this->assertCount(20, $result->items()); // Default per page
        $this->assertEquals(25, $result->total());
    }
}
