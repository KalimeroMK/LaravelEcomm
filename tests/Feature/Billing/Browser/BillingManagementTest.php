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
    Invoice::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get('/billing/history');

    $response->assertStatus(200);
    $response->assertSee('Billing History');
});

test('user can view invoice details', function () {
    $invoice = Invoice::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100.00,
        'status' => 'paid',
    ]);

    $response = $this->actingAs($this->user)
        ->get("/billing/invoices/{$invoice->id}");

    $response->assertStatus(200);
    $response->assertSee('$100.00');
});

test('user can download invoice PDF', function () {
    $invoice = Invoice::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get("/billing/invoices/{$invoice->id}/download");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('admin can view all invoices', function () {
    Invoice::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/billing/invoices');

    $response->assertStatus(200);
    $response->assertSee('Invoices');
});

test('admin can create invoice', function () {
    $invoiceData = [
        'user_id' => $this->user->id,
        'amount' => 150.00,
        'description' => 'Test Invoice',
        'due_date' => now()->addDays(30)->format('Y-m-d'),
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/billing/invoices', $invoiceData);

    $response->assertRedirect();
    $this->assertDatabaseHas('invoices', [
        'user_id' => $this->user->id,
        'amount' => 150.00,
    ]);
});

test('admin can update invoice status', function () {
    $invoice = Invoice::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/billing/invoices/{$invoice->id}", [
            'status' => 'paid',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
    ]);
});

test('payment processing works', function () {
    $invoice = Invoice::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100.00,
        'status' => 'pending',
    ]);

    $paymentData = [
        'invoice_id' => $invoice->id,
        'amount' => 100.00,
        'method' => 'paypal',
        'transaction_id' => 'PAY-123456789',
    ];

    $response = $this->actingAs($this->user)
        ->post('/billing/payments', $paymentData);

    $response->assertRedirect();
    $this->assertDatabaseHas('payments', [
        'invoice_id' => $invoice->id,
        'amount' => 100.00,
        'method' => 'paypal',
    ]);

    $invoice->refresh();
    expect($invoice->status)->toBe('paid');
});

test('user can view payment history', function () {
    Payment::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get('/billing/payments');

    $response->assertStatus(200);
    $response->assertSee('Payment History');
});

test('admin can view payment analytics', function () {
    Payment::factory()->count(10)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/billing/analytics');

    $response->assertStatus(200);
    $response->assertSee('Payment Analytics');
});
