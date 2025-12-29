@extends('front::layouts.master')

@section('title', 'Track Order')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Track Order</h2>

                <div class="card">
                    <div class="card-header">
                        <h5>Order #{{ $order->order_number }}</h5>
                        <a href="{{ route('user.orders.history') }}" class="btn btn-sm btn-secondary">Back to Orders</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Order Number:</strong></td>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Order Date:</strong></td>
                                        <td>{{ $order->created_at->format('F d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Current Status</h5>
                                <div class="status-display">
                                    <span class="badge badge-lg badge-{{ 
                                        $order->status == 'pending' ? 'warning' : 
                                        ($order->status == 'processing' ? 'info' : 
                                        ($order->status == 'shipped' ? 'primary' : 
                                        ($order->status == 'delivered' ? 'success' : 'danger'))) 
                                    }}" style="font-size: 1.2em; padding: 10px 20px;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($order->tracking_number)
                            <div class="alert alert-info mb-4">
                                <h5>Tracking Information</h5>
                                <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                                @if($order->tracking_carrier)
                                    <p><strong>Carrier:</strong> {{ $order->tracking_carrier }}</p>
                                @endif
                                @if($order->shipped_at)
                                    <p><strong>Shipped On:</strong> {{ $order->shipped_at->format('F d, Y g:i A') }}</p>
                                @endif
                            </div>
                        @endif

                        <h5 class="mb-3">Order Status Timeline</h5>
                        <div class="timeline">
                            <div class="timeline-item {{ $order->status == 'pending' || $order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered' ? 'active' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Order Placed</h6>
                                    <p>{{ $order->created_at->format('F d, Y g:i A') }}</p>
                                </div>
                            </div>

                            <div class="timeline-item {{ $order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered' ? 'active' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Processing</h6>
                                    <p>Your order is being prepared</p>
                                </div>
                            </div>

                            <div class="timeline-item {{ $order->status == 'shipped' || $order->status == 'delivered' ? 'active' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Shipped</h6>
                                    <p>Your order has been shipped</p>
                                </div>
                            </div>

                            <div class="timeline-item {{ $order->status == 'delivered' ? 'active' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Delivered</h6>
                                    <p>Your order has been delivered</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-primary">View Order Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: -30px;
            width: 2px;
            background: #ddd;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-item.active::before {
            background: #28a745;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ddd;
            border: 3px solid #fff;
        }

        .timeline-item.active .timeline-marker {
            background: #28a745;
        }

        .timeline-content h6 {
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .timeline-content p {
            margin: 0;
            color: #666;
        }
    </style>
@endpush

