@extends('front::layouts.master')

@section('title','Order Details - ' . ($order->order_number ?? $order->id))

@section('content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{route('front.my-orders')}}">My Orders<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Order #{{ $order->order_number ?? $order->id }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Order Detail Section -->
    <section class="order-detail section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <!-- Download Links for Digital Products -->
                    @include('front::partials.download-links')
                    
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Order #{{ $order->order_number ?? $order->id }}</h4>
                            <div>
                                <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }} mr-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Order Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Order Date:</strong></td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ strtoupper($order->payment_method) }}</td>
                                        </tr>
                                        @if($order->transaction_reference)
                                            <tr>
                                                <td><strong>Transaction ID:</strong></td>
                                                <td>{{ $order->transaction_reference }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Shipping Address</h6>
                                    <address>
                                        <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                                        {{ $order->address1 }}<br>
                                        @if($order->address2)
                                            {{ $order->address2 }}<br>
                                        @endif
                                        {{ $order->city }}, {{ $order->state }} {{ $order->post_code }}<br>
                                        {{ $order->country }}<br>
                                        @if($order->phone)
                                            <abbr title="Phone">P:</abbr> {{ $order->phone }}
                                        @endif
                                    </address>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->carts as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product)
                                                        <a href="{{ route('front.product-detail', $item->product->slug) }}">
                                                            {{ $item->product->title }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Product no longer available</span>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                            <td>${{ number_format($order->sub_total, 2) }}</td>
                                        </tr>
                                        @if($order->shipping)
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Shipping ({{ $order->shipping->type }}):</strong></td>
                                                <td>${{ number_format($order->shipping->price, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('front.my-orders') }}" class="btn btn-secondary">
                                    <i class="ti-arrow-left"></i> Back to Orders
                                </a>
                                <form action="{{ route('front.reorder', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti-reload"></i> Reorder
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Order Detail Section -->

@endsection
