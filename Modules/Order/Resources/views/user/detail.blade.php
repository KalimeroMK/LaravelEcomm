@extends('front::layouts.master')

@section('title', 'Order Details')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Order Details</h2>

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
                                        <td><strong>Status:</strong></td>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $order->payment_status == 'paid' ? 'success' : 
                                                ($order->payment_status == 'pending' ? 'warning' : 'danger') 
                                            }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>{{ ucfirst($order->payment_method) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $order->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $order->user->email }}</td>
                                    </tr>
                                    @if($order->shipping)
                                        <tr>
                                            <td><strong>Shipping Method:</strong></td>
                                            <td>{{ $order->shipping->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <h5 class="mb-3">Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->carts as $cart)
                                        <tr>
                                            <td>
                                                @if($cart->product)
                                                    <strong>{{ $cart->product->title }}</strong>
                                                @else
                                                    Product #{{ $cart->product_id }}
                                                @endif
                                            </td>
                                            <td>{{ $cart->quantity }}</td>
                                            <td>${{ number_format($cart->price, 2) }}</td>
                                            <td>${{ number_format($cart->quantity * $cart->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td><strong>${{ number_format($order->sub_total, 2) }}</strong></td>
                                    </tr>
                                    @if($order->shipping)
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Shipping:</strong></td>
                                            <td><strong>${{ number_format($order->shipping->price ?? 0, 2) }}</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('user.orders.track', $order) }}" class="btn btn-primary">Track Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

