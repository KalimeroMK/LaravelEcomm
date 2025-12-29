@extends('admin::layouts.master')

@section('title', __('Billing History'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Billing History') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="billingTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab">
                                    {{ __('Invoices') }} ({{ $invoices->count() }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments" role="tab">
                                    {{ __('Payments') }} ({{ $payments->count() }})
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="billingTabsContent">
                            <div class="tab-pane fade show active" id="invoices" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Invoice Number') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->invoice_number }}</td>
                                                    <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                                                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->isOverdue() ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($invoice->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                            {{ __('View') }}
                                                        </a>
                                                        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-primary">
                                                            {{ __('Download PDF') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('No invoices found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Method') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Transaction ID') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $payment->transaction_id ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('No payments found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

