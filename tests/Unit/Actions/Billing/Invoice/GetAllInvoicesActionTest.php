<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Invoice;

use Illuminate\Support\Collection;
use Modules\Billing\Actions\Invoice\GetAllInvoicesAction;
use Modules\Billing\Models\Invoice;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllInvoicesActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllInvoices(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Invoice::factory()->count(3)->create(['user_id' => $user1->id]);
        Invoice::factory()->count(2)->create(['user_id' => $user2->id]);

        $action = app(GetAllInvoicesAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(5, $result);
    }

    public function testExecuteReturnsInvoicesForSpecificUser(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Invoice::factory()->count(3)->create(['user_id' => $user1->id]);
        Invoice::factory()->count(2)->create(['user_id' => $user2->id]);

        $action = app(GetAllInvoicesAction::class);
        $result = $action->execute($user1->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        
        foreach ($result as $invoice) {
            $this->assertEquals($user1->id, $invoice->user_id);
        }
    }

    public function testExecuteReturnsEmptyCollectionWhenNoInvoices(): void
    {
        $user = User::factory()->create();

        $action = app(GetAllInvoicesAction::class);
        $result = $action->execute($user->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsInvoicesOrderedByCreatedAtDesc(): void
    {
        $user = User::factory()->create();

        $invoice1 = Invoice::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays(2)]);
        $invoice2 = Invoice::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);
        $invoice3 = Invoice::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

        $action = app(GetAllInvoicesAction::class);
        $result = $action->execute($user->id);

        $this->assertEquals($invoice3->id, $result->first()->id);
        $this->assertEquals($invoice1->id, $result->last()->id);
    }
}
