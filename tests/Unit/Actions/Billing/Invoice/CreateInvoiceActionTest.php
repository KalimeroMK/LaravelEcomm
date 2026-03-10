<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Invoice;

use Illuminate\Support\Carbon;
use Modules\Billing\Actions\Invoice\CreateInvoiceAction;
use Modules\Billing\DTOs\InvoiceDTO;
use Modules\Billing\Models\Invoice;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreateInvoiceActionTest extends ActionTestCase
{
    public function testExecuteCreatesInvoiceSuccessfully(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $dto = new InvoiceDTO(
            id: null,
            invoice_number: null,
            order_id: $order->id,
            user_id: $user->id,
            status: 'draft',
            issue_date: Carbon::now(),
            due_date: Carbon::now()->addDays(30),
            paid_date: null,
            subtotal: 100.00,
            tax_amount: 10.00,
            discount_amount: 5.00,
            total_amount: 105.00,
            notes: 'Test invoice notes',
        );

        $action = app(CreateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($order->id, $result->order_id);
        $this->assertEquals('draft', $result->status);
        $this->assertEquals(105.00, $result->total_amount);
        $this->assertDatabaseHas('invoices', ['user_id' => $user->id, 'order_id' => $order->id]);
    }

    public function testExecuteGeneratesInvoiceNumberWhenNotProvided(): void
    {
        $user = User::factory()->create();

        $dto = new InvoiceDTO(
            id: null,
            invoice_number: null,
            order_id: null,
            user_id: $user->id,
            status: 'sent',
            issue_date: Carbon::now(),
            due_date: Carbon::now()->addDays(30),
            paid_date: null,
            subtotal: 50.00,
            tax_amount: 5.00,
            discount_amount: 0.00,
            total_amount: 55.00,
            notes: null,
        );

        $action = app(CreateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->invoice_number);
        $this->assertStringStartsWith('INV-', $result->invoice_number);
        $this->assertEquals(10, strlen(str_replace('INV-', '', $result->invoice_number)));
    }

    public function testExecuteUsesProvidedInvoiceNumber(): void
    {
        $user = User::factory()->create();

        $dto = new InvoiceDTO(
            id: null,
            invoice_number: 'CUSTOM-INV-12345',
            order_id: null,
            user_id: $user->id,
            status: 'sent',
            issue_date: Carbon::now(),
            due_date: Carbon::now()->addDays(30),
            paid_date: null,
            subtotal: 50.00,
            tax_amount: 5.00,
            discount_amount: 0.00,
            total_amount: 55.00,
            notes: null,
        );

        $action = app(CreateInvoiceAction::class);
        $result = $action->execute($dto);

        $this->assertEquals('CUSTOM-INV-12345', $result->invoice_number);
    }
}
