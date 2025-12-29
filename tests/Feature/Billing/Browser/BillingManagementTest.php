<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\User\Models\User;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = createAdminUser();
});

test('user can view billing history', function () {
    Invoice::factory()->count(3)->create(['user_id' => $this->user->id]);
    Payment::factory()->count(2)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('billing.history'));

    $response->assertStatus(200);
    $response->assertSee('Billing History');
});

test('user can view invoice details', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('invoices.show', $invoice));

    $response->assertStatus(200);
    $response->assertSee($invoice->invoice_number);
});

test('user can download invoice PDF', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('invoices.download', $invoice));

    $response->assertStatus(200);
    $response->assertSee($invoice->invoice_number);
});

test('admin can view all invoices', function () {
    Invoice::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('invoices.index'));

    $response->assertStatus(200);
    $response->assertSee('Invoices');
});

test('admin can create invoice', function () {
    $order = Modules\Order\Models\Order::factory()->create();

    $response = $this->actingAs($this->admin)
        ->post(route('invoices.store'), [
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'status' => 'draft',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 100.00,
            'tax_amount' => 10.00,
            'discount_amount' => 5.00,
            'total_amount' => 105.00,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('invoices', [
        'user_id' => $this->user->id,
        'total_amount' => 105.00,
    ]);
});

test('admin can update invoice status', function () {
    $invoice = Invoice::factory()->create(['status' => 'draft']);

    $response = $this->actingAs($this->admin)
        ->put(route('invoices.update', $invoice), [
            'user_id' => $invoice->user_id,
            'status' => 'paid',
            'issue_date' => $invoice->issue_date->format('Y-m-d'),
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'subtotal' => $invoice->subtotal,
            'tax_amount' => $invoice->tax_amount,
            'discount_amount' => $invoice->discount_amount,
            'total_amount' => $invoice->total_amount,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
    ]);
});

test('payment processing works', function () {
    $order = Modules\Order\Models\Order::factory()->create(['user_id' => $this->user->id]);

    $payment = Payment::factory()->create([
        'order_id' => $order->id,
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    expect($payment->isPending())->toBeTrue();
    expect($payment->isCompleted())->toBeFalse();
});

test('user can view payment history', function () {
    Payment::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('payments.history'));

    $response->assertStatus(200);
    $response->assertSee('Payment History');
});

test('admin can view payment analytics', function () {
    Payment::factory()->count(5)->create(['status' => 'completed', 'amount' => 100.00]);

    $response = $this->actingAs($this->admin)
        ->get(route('payments.analytics'));

    $response->assertStatus(200);
    // Check for analytics data instead of exact text
    $response->assertSee('Total Revenue', false);
    $response->assertSee('Total Payments', false);
});
