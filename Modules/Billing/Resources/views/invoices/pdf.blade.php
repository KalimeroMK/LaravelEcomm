<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 20px; }
        .invoice-info table { width: 100%; }
        .invoice-items { margin-top: 30px; }
        .invoice-items table { width: 100%; border-collapse: collapse; }
        .invoice-items th, .invoice-items td { border: 1px solid #ddd; padding: 8px; }
        .text-right { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice #{{ $invoice->invoice_number }}</h1>
    </div>
    
    <div class="invoice-info">
        <table>
            <tr>
                <td>
                    <strong>Issue Date:</strong> {{ $invoice->issue_date->format('Y-m-d') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}<br>
                    <strong>Status:</strong> {{ ucfirst($invoice->status) }}
                </td>
                <td class="text-right">
                    <strong>Customer:</strong><br>
                    {{ $invoice->user->name ?? '-' }}<br>
                    {{ $invoice->user->email ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    
    <div class="invoice-items">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Order Total</td>
                    <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                    <td class="text-right">${{ number_format($invoice->discount_amount, 2) }}</td>
                    <td class="text-right total">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    @if($invoice->notes)
        <div style="margin-top: 30px;">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
    @endif
</body>
</html>

