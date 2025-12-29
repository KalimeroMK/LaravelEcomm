@extends('front::layouts.master')

@section('title', 'My Orders')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">My Orders</h2>

                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $order->status == 'pending' ? 'warning' : 
                                                ($order->status == 'processing' ? 'info' : 
                                                ($order->status == 'shipped' ? 'primary' : 
                                                ($order->status == 'delivered' ? 'success' : 'danger'))) 
                                            }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $order->payment_status == 'paid' ? 'success' : 
                                                ($order->payment_status == 'pending' ? 'warning' : 'danger') 
                                            }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('user.orders.track', $order) }}" class="btn btn-sm btn-info">Track</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->links('pagination::admin-bootstrap-5') }}
                    </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <p>You haven't placed any orders yet.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Start Shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

