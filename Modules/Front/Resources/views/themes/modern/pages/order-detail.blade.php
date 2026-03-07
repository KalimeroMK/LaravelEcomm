@php
$activeTheme = 'modern';
$themePath = 'front::themes.' . $activeTheme;
@endphp
@extends($themePath . '.layouts.master')

@section('title','Order Details - ' . ($order->order_number ?? $order->id))

@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li><a href="{{ route('front.my-orders') }}">My Orders</a></li>
            <li class="active">Order #{{ $order->order_number ?? $order->id }}</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Order #{{ $order->order_number ?? $order->id }}
                            <span class="pull-right">
                                <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Order Information</h4>
                                <table class="table table-condensed">
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
                                <h4>Shipping Address</h4>
                                <address>
                                    <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                                    {{ $order->address1 }}<br>
                                    @if($order->address2)
                                        {{ $order->address2 }}<br>
                                    @endif
                                    {{ $order->city }}, {{ $order->post_code }}<br>
                                    {{ $order->country }}<br>
                                    @if($order->phone)
                                        <abbr title="Phone">P:</abbr> {{ $order->phone }}
                                    @endif
                                </address>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h4>Order Items</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
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
                        
                        <div class="space-bottom"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('front.my-orders') }}" class="btn btn-default">
                                    <i class="icon-left-open-big"></i> Back to Orders
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <form action="{{ route('front.reorder', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Reorder
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
