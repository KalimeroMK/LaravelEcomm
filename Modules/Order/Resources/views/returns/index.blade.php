@extends('admin::layouts.master')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Return / Refund Requests</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if($returns->isNotEmpty())
                    <table class="table table-bordered" id="data-table" data-server-paginated>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th style="min-width:260px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($returns as $return)
                            <tr>
                                <td>{{ $return->id }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $return->order_id) }}">
                                        {{ $return->order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $return->order->user->name ?? $return->order->email ?? '—' }}</td>
                                <td style="max-width:280px">{{ \Illuminate\Support\Str::limit($return->reason, 120) }}</td>
                                <td>{{ number_format((float) ($return->refund_amount ?? $return->order->total_amount), 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ ['requested' => 'warning', 'approved' => 'info', 'rejected' => 'secondary', 'refunded' => 'success'][$return->status] ?? 'light' }}">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($return->status === \Modules\Order\Models\OrderReturn::STATUS_REQUESTED)
                                        <form method="POST" action="{{ route('order-returns.approve', $return) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-info">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('order-returns.reject', $return) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary">Reject</button>
                                        </form>
                                    @endif
                                    @if($return->isOpen())
                                        <form method="POST" action="{{ route('order-returns.refund', $return) }}" class="d-inline form-inline">
                                            @csrf
                                            <input type="number" step="0.01" min="0.01" name="refund_amount"
                                                   class="form-control form-control-sm mr-1" style="width:110px"
                                                   placeholder="{{ number_format((float) $return->order->total_amount, 2) }}">
                                            <button class="btn btn-sm btn-success"
                                                    onclick="return confirm('Refund this order and restore stock?');">
                                                Refund
                                            </button>
                                        </form>
                                    @endif
                                    @if($return->refund_reference)
                                        <small class="text-muted d-block">Ref: {{ $return->refund_reference }}</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $returns->links() }}
                    </div>
                @else
                    <h6 class="text-center">No return requests yet.</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
