<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Complaint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Complaint\Actions\FindComplaintAction;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindComplaintActionTest extends ActionTestCase
{
    public function test_execute_finds_complaint_by_id(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Findable complaint',
            'status' => 'pending',
        ]);

        $repository = new ComplaintRepository();
        $action = new FindComplaintAction($repository);

        // Act
        $result = $action->execute($complaint->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals($complaint->id, $result->id);
        $this->assertEquals('Findable complaint', $result->description);
        $this->assertEquals('pending', $result->status);
    }

    public function test_execute_finds_complaint_with_all_attributes(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Complete complaint description',
            'status' => 'resolved',
        ]);

        $repository = new ComplaintRepository();
        $action = new FindComplaintAction($repository);

        // Act
        $result = $action->execute($complaint->id);

        // Assert
        $this->assertEquals($complaint->id, $result->id);
        $this->assertEquals($complaint->user_id, $result->user_id);
        $this->assertEquals($complaint->order_id, $result->order_id);
        $this->assertEquals('Complete complaint description', $result->description);
        $this->assertEquals('resolved', $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_complaint(): void
    {
        // Arrange
        $repository = new ComplaintRepository();
        $action = new FindComplaintAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }

    public function test_execute_finds_complaint_with_relations(): void
    {
        // Arrange
        $complaint = Complaint::factory()->create([
            'description' => 'Complaint with relations',
        ]);

        $repository = new ComplaintRepository();
        $action = new FindComplaintAction($repository);

        // Act
        $result = $action->execute($complaint->id);

        // Assert
        $this->assertInstanceOf(Complaint::class, $result);
        $this->assertEquals($complaint->id, $result->id);
        $this->assertEquals('Complaint with relations', $result->description);
    }
}
