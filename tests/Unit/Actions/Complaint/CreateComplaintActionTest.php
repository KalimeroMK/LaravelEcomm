<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Complaint;

use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreateComplaintActionTest extends ActionTestCase
{
    public function test_execute_creates_complaint_with_dto(): void
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        
        $repository = new ComplaintRepository();
        $action = new CreateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: null,
            user_id: $user->id,
            order_id: $order->id,
            description: 'This is a test complaint description.',
            status: 'pending',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($order->id, $result->order_id);
        $this->assertEquals('This is a test complaint description.', $result->description);
        $this->assertEquals('pending', $result->status);
        $this->assertDatabaseHas('complaints', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'description' => 'This is a test complaint description.',
            'status' => 'pending',
        ]);
    }

    public function test_execute_creates_complaint_with_resolved_status(): void
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        
        $repository = new ComplaintRepository();
        $action = new CreateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: null,
            user_id: $user->id,
            order_id: $order->id,
            description: 'This complaint is resolved.',
            status: 'resolved',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals('resolved', $result->status);
        $this->assertDatabaseHas('complaints', [
            'description' => 'This complaint is resolved.',
            'status' => 'resolved',
        ]);
    }

    public function test_execute_creates_complaint_from_array(): void
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        
        $repository = new ComplaintRepository();
        $action = new CreateComplaintAction($repository);

        $dto = ComplaintDTO::fromArray([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'description' => 'Created from array.',
            'status' => 'in_review',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Created from array.', $result->description);
        $this->assertEquals('in_review', $result->status);
    }

    public function test_execute_creates_complaint_with_default_status(): void
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        
        $repository = new ComplaintRepository();
        $action = new CreateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: null,
            user_id: $user->id,
            order_id: $order->id,
            description: 'Complaint with default status.',
            status: 'pending',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals('pending', $result->status);
        $this->assertDatabaseHas('complaints', [
            'description' => 'Complaint with default status.',
            'status' => 'pending',
        ]);
    }
}
