<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Payment;

use Modules\Billing\Actions\Payment\GetPaymentAnalyticsAction;
use Modules\Billing\Models\Payment;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetPaymentAnalyticsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAnalyticsArray(): void
    {
        $user = User::factory()->create();

        // Create completed payments
        Payment::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 100.00,
        ]);

        // Create pending payments
        Payment::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'amount' => 50.00,
        ]);

        // Create failed payments
        Payment::factory()->count(1)->create([
            'user_id' => $user->id,
            'status' => 'failed',
            'amount' => 25.00,
        ]);

        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_amount', $result);
        $this->assertArrayHasKey('total_count', $result);
        $this->assertArrayHasKey('pending_count', $result);
        $this->assertArrayHasKey('failed_count', $result);
        $this->assertArrayHasKey('success_rate', $result);
    }

    public function testExecuteCalculatesTotalAmountCorrectly(): void
    {
        $user = User::factory()->create();

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 100.00,
        ]);
        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 200.00,
        ]);

        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        $this->assertEquals(300.00, $result['total_amount']);
    }

    public function testExecuteCountsStatusesCorrectly(): void
    {
        $user = User::factory()->create();

        Payment::factory()->count(5)->create(['user_id' => $user->id, 'status' => 'completed']);
        Payment::factory()->count(3)->create(['user_id' => $user->id, 'status' => 'pending']);
        Payment::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'failed']);

        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        $this->assertEquals(5, $result['total_count']);
        $this->assertEquals(3, $result['pending_count']);
        $this->assertEquals(2, $result['failed_count']);
    }

    public function testExecuteCalculatesSuccessRateCorrectly(): void
    {
        $user = User::factory()->create();

        // 8 completed, 2 failed = 80% success rate
        Payment::factory()->count(8)->create(['user_id' => $user->id, 'status' => 'completed']);
        Payment::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'failed']);

        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        $this->assertEquals(80.0, $result['success_rate']);
    }

    public function testExecuteReturnsZeroSuccessRateWhenNoPayments(): void
    {
        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        $this->assertEquals(0, $result['total_count']);
        $this->assertEquals(0, $result['success_rate']);
    }

    public function testExecuteOnlyIncludesCompletedPaymentsInTotalAmount(): void
    {
        $user = User::factory()->create();

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 100.00,
        ]);
        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'amount' => 200.00,
        ]);
        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => 'failed',
            'amount' => 300.00,
        ]);

        $action = app(GetPaymentAnalyticsAction::class);
        $result = $action->execute();

        // Only completed payments count towards total_amount
        $this->assertEquals(100.00, $result['total_amount']);
    }
}
