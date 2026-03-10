<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Complaint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Complaint\Actions\UpdateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateComplaintActionTest extends ActionTestCase
{
    public function test_execute_updates_complaint_with_dto(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Original description',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new UpdateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: $complaint->id,
            user_id: $complaint->user_id,
            order_id: $complaint->order_id,
            description: 'Updated description',
            status: 'resolved',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals('Updated description', $result->description);
        $this->assertEquals('resolved', $result->status);
        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'description' => 'Updated description',
            'status' => 'resolved',
        ]);
    }

    public function test_execute_updates_only_description(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Original description',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new UpdateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: $complaint->id,
            user_id: $complaint->user_id,
            order_id: $complaint->order_id,
            description: 'Only description updated',
            status: 'pending',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Only description updated', $result->description);
        $this->assertEquals('pending', $result->status);
    }

    public function test_execute_updates_only_status(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Description unchanged',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new UpdateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: $complaint->id,
            user_id: $complaint->user_id,
            order_id: $complaint->order_id,
            description: 'Description unchanged',
            status: 'in_review',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Description unchanged', $result->description);
        $this->assertEquals('in_review', $result->status);
    }

    public function test_execute_updates_complaint_from_array(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Original',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new UpdateComplaintAction($repository);

        $dto = ComplaintDTO::fromArray([
            'id' => $complaint->id,
            'user_id' => $complaint->user_id,
            'order_id' => $complaint->order_id,
            'description' => 'Updated from array',
            'status' => 'resolved',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Updated from array', $result->description);
        $this->assertEquals('resolved', $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_complaint(): void
    {
        // Arrange
        $repository = new ComplaintRepository();
        $action = new UpdateComplaintAction($repository);

        $dto = new ComplaintDTO(
            id: 999999,
            user_id: 1,
            order_id: 1,
            description: 'Nonexistent complaint',
            status: 'pending',
        );

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute($dto);
    }
}
