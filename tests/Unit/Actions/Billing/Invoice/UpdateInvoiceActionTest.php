<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Invoice;

use Illuminate\Support\Carbon;
use Modules\Billing\Actions\Invoice\UpdateInvoiceAction;
use Modules\Billing\DTOs\InvoiceDTO;
use Modules\Billing\Models\Invoice;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateInvoiceActionTest extends ActionTestCase
{
    public function testExecuteUpdatesInvoiceSuccessfully(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'total_amount' => 100.00,
        ]);

        $dto = new InvoiceDTO(
            id: $invoice->id,
            invoice_number: $invoice->invoice_number,
            order_id: $invoice->order_id,
            user_id: $user->id,
            status: 'paid',
            issue_date: $invoice->issue_date,
            due_date: $invoice->due_date,
            paid_date: Carbon::now(),
            subtotal: 100.00,
            tax_amount: 10.00,
            discount_amount: 5.00,
            total_amount: 105.00,
            notes: 'Updated notes',
        );

        $action = app(UpdateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($invoice->id, $result->id);
        $this->assertEquals('paid', $result->status);
        $this->assertEquals(105.00, $result->total_amount);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'total_amount' => 105.00,
        ]);
    }

    public function testExecuteUpdatesPartialFields(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'notes' => 'Original notes',
        ]);

        $dto = new InvoiceDTO(
            id: $invoice->id,
            invoice_number: null,
            order_id: null,
            user_id: null,
            status: 'sent',
            issue_date: null,
            due_date: null,
            paid_date: null,
            subtotal: null,
            tax_amount: null,
            discount_amount: null,
            total_amount: null,
            notes: 'Updated notes only',
        );

        $action = app(UpdateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('sent', $result->status);
        $this->assertEquals('Updated notes only', $result->notes);
    }

    public function testExecuteReturnsUpdatedInvoiceInstance(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $dto = new InvoiceDTO(
            id: $invoice->id,
            invoice_number: null,
            order_id: null,
            user_id: null,
            status: 'viewed',
            issue_date: null,
            due_date: null,
            paid_date: null,
            subtotal: null,
            tax_amount: null,
            discount_amount: null,
            total_amount: null,
            notes: null,
        );

        $action = app(UpdateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertTrue($result->exists);
    }
}
