<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Complaint;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Complaint\Actions\GetAllComplaintsAction;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllComplaintsActionTest extends ActionTestCase
{
    public function test_execute_returns_empty_paginator_when_no_user(): void
    {
        // Arrange
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(0, $result);
        $this->assertEquals(0, $result->total());
    }

    public function test_execute_returns_user_complaints_for_regular_user(): void
    {
        // Arrange
        $this->seedPermissions();
        
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Create complaints for both users
        Complaint::factory()->count(3)->create(['user_id' => $user->id]);
        Complaint::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);
        
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(3, $result);
        foreach ($result as $complaint) {
            $this->assertEquals($user->id, $complaint->user_id);
        }
    }

    public function test_execute_returns_all_complaints_for_admin_user(): void
    {
        // Arrange
        $this->seedPermissions();
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $user = User::factory()->create();
        
        Complaint::factory()->count(3)->create(['user_id' => $user->id]);
        Complaint::factory()->count(2)->create(['user_id' => $admin->id]);

        $this->actingAs($admin);
        
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result);
    }

    public function test_execute_returns_all_complaints_for_super_admin_user(): void
    {
        // Arrange
        $this->seedPermissions();
        
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');
        
        Complaint::factory()->count(4)->create();

        $this->actingAs($superAdmin);
        
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(4, $result);
    }

    public function test_execute_returns_complaints_ordered_by_id_desc(): void
    {
        // Arrange
        $this->seedPermissions();
        
        $user = User::factory()->create();
        
        $complaint1 = Complaint::factory()->create(['user_id' => $user->id]);
        $complaint2 = Complaint::factory()->create(['user_id' => $user->id]);
        $complaint3 = Complaint::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $ids = $result->pluck('id')->toArray();
        $this->assertEquals([$complaint3->id, $complaint2->id, $complaint1->id], $ids);
    }

    public function test_execute_returns_complaints_with_pagination(): void
    {
        // Arrange
        $this->seedPermissions();
        
        $user = User::factory()->create();
        Complaint::factory()->count(25)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        
        $repository = new ComplaintRepository();
        $action = new GetAllComplaintsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(20, $result); // Default per page is 20
        $this->assertEquals(25, $result->total());
    }
}
