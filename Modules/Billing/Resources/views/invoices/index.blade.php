@extends('admin::layouts.master')

@section('title', __('Invoices'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Invoices') }}</h3>
                        @can('create', \Modules\Billing\Models\Invoice::class)
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary float-right">
                                {{ __('Create Invoice') }}
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Invoice Number') }}</th>
                                        <th>{{ __('User') }}</th>
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
                                            <td>{{ $invoice->user->name ?? '-' }}</td>
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
                                                @can('update', $invoice)
                                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                                        {{ __('Edit') }}
                                                    </a>
                                                @endcan
                                                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-primary">
                                                    {{ __('Download PDF') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('No invoices found') }}</td>
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
@endsection

