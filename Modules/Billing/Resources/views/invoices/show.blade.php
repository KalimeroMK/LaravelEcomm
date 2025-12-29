@extends('admin::layouts.master')

@section('title', __('Invoice Details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Invoice') }} #{{ $invoice->invoice_number }}</h3>
                        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-primary float-right">
                            {{ __('Download PDF') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ __('Invoice Information') }}</h5>
                                <p><strong>{{ __('Invoice Number') }}:</strong> {{ $invoice->invoice_number }}</p>
                                <p><strong>{{ __('Status') }}:</strong> 
                                    <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->isOverdue() ? 'danger' : 'warning') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </p>
                                <p><strong>{{ __('Issue Date') }}:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</p>
                                <p><strong>{{ __('Due Date') }}:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
                                @if($invoice->paid_date)
                                    <p><strong>{{ __('Paid Date') }}:</strong> {{ $invoice->paid_date->format('Y-m-d') }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5>{{ __('Customer Information') }}</h5>
                                <p><strong>{{ __('Name') }}:</strong> {{ $invoice->user->name ?? '-' }}</p>
                                <p><strong>{{ __('Email') }}:</strong> {{ $invoice->user->email ?? '-' }}</p>
                                @if($invoice->order)
                                    <p><strong>{{ __('Order Number') }}:</strong> {{ $invoice->order->order_number ?? '-' }}</p>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5>{{ __('Invoice Items') }}</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Description') }}</th>
                                            <th class="text-right">{{ __('Subtotal') }}</th>
                                            <th class="text-right">{{ __('Tax') }}</th>
                                            <th class="text-right">{{ __('Discount') }}</th>
                                            <th class="text-right">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ __('Order Total') }}</td>
                                            <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                                            <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                                            <td class="text-right">${{ number_format($invoice->discount_amount, 2) }}</td>
                                            <td class="text-right"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($invoice->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>{{ __('Notes') }}</h5>
                                    <p>{{ $invoice->notes }}</p>
                                </div>
                            </div>
                        @endif
                        @if($invoice->payments->count() > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>{{ __('Payments') }}</h5>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Method') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

