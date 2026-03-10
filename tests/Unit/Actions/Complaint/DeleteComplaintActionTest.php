<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Complaint;

use Modules\Complaint\Actions\DeleteComplaintAction;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteComplaintActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_complaint(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Complaint to delete',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new DeleteComplaintAction($repository);

        // Act
        $action->execute($complaint->id);

        // Assert
        $this->assertDatabaseMissing('complaints', [
            'id' => $complaint->id,
            'description' => 'Complaint to delete',
        ]);
    }

    public function test_execute_deletes_complaint_and_verifies_count(): void
    {
        // Arrange
        $complaint1 = Complaint::factory()->create();
        $complaint2 = Complaint::factory()->create();
        $complaint3 = Complaint::factory()->create();

        $repository = new ComplaintRepository();
        $action = new DeleteComplaintAction($repository);

        // Act
        $action->execute($complaint2->id);

        // Assert
        $this->assertDatabaseHas('complaints', ['id' => $complaint1->id]);
        $this->assertDatabaseMissing('complaints', ['id' => $complaint2->id]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint3->id]);
        $this->assertEquals(2, Complaint::count());
    }

    public function test_execute_deletes_complaint_with_different_statuses(): void
    {
        // Arrange
        $pendingComplaint = Complaint::factory()->create(['status' => 'pending']);
        $resolvedComplaint = Complaint::factory()->create(['status' => 'resolved']);
        $inReviewComplaint = Complaint::factory()->create(['status' => 'in_review']);

        $repository = new ComplaintRepository();
        $action = new DeleteComplaintAction($repository);

        // Act
        $action->execute($resolvedComplaint->id);

        // Assert
        $this->assertDatabaseHas('complaints', ['id' => $pendingComplaint->id]);
        $this->assertDatabaseMissing('complaints', ['id' => $resolvedComplaint->id]);
        $this->assertDatabaseHas('complaints', ['id' => $inReviewComplaint->id]);
    }
}
