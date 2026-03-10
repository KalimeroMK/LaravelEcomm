<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Payment;

use Illuminate\Support\Collection;
use Modules\Billing\Actions\Payment\GetUserPaymentsAction;
use Modules\Billing\Models\Payment;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetUserPaymentsActionTest extends ActionTestCase
{
    public function testExecuteReturnsUserPayments(): void
    {
        $user = User::factory()->create();

        Payment::factory()->count(3)->create(['user_id' => $user->id]);

        $action = app(GetUserPaymentsAction::class);
        $result = $action->execute($user->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsOnlySpecifiedUserPayments(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Payment::factory()->count(3)->create(['user_id' => $user1->id]);
        Payment::factory()->count(2)->create(['user_id' => $user2->id]);

        $action = app(GetUserPaymentsAction::class);
        $result = $action->execute($user1->id);

        $this->assertCount(3, $result);

        foreach ($result as $payment) {
            $this->assertEquals($user1->id, $payment->user_id);
        }
    }

    public function testExecuteReturnsEmptyCollectionWhenUserHasNoPayments(): void
    {
        $user = User::factory()->create();

        $action = app(GetUserPaymentsAction::class);
        $result = $action->execute($user->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsPaymentsOrderedByCreatedAtDesc(): void
    {
        $user = User::factory()->create();

        $payment1 = Payment::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays(2)]);
        $payment2 = Payment::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);
        $payment3 = Payment::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

        $action = app(GetUserPaymentsAction::class);
        $result = $action->execute($user->id);

        $this->assertEquals($payment3->id, $result->first()->id);
        $this->assertEquals($payment1->id, $result->last()->id);
    }
}
