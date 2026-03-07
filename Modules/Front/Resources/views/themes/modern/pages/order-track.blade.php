@php
$activeTheme = 'modern';
$themePath = 'front::themes.' . $activeTheme;
@endphp
@extends($themePath . '.layouts.master')

@section('title','Track Order #' . ($order->order_number ?? $order->id))

@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li><a href="{{ route('user.orders.history') }}">My Orders</a></li>
            <li class="active">Track Order #{{ $order->order_number ?? $order->id }}</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">Order Tracking</h1>
                <div class="separator-2"></div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Order #{{ $order->order_number ?? $order->id }}
                            <span class="pull-right">
                                <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Tracking Information</h4>
                                <div class="space-bottom"></div>
                                
                                @if($order->shipping)
                                    <p><strong>Shipping Method:</strong> {{ $order->shipping->type }}</p>
                                    @if($order->shipping->tracking_number)
                                        <p><strong>Tracking Number:</strong> {{ $order->shipping->tracking_number }}</p>
                                    @endif
                                @else
                                    <p>No shipping information available yet.</p>
                                @endif
                                
                                <div class="space-bottom"></div>
                                
                                <h5>Order Status History</h5>
                                <div class="timeline">
                                    <div class="timeline-item {{ $order->status == 'pending' || $order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered' ? 'active' : '' }}">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6>Order Placed</h6>
                                            <p>{{ $order->created_at->format('M d, Y H:i') }}</p>
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
                                            <p>Your order is on the way</p>
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
                            </div>
                        </div>
                        
                        <div class="space-bottom"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('user.orders.history') }}" class="btn btn-default">
                                    <i class="icon-left-open-big"></i> Back to Orders
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-default">
                                    <i class="fa fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
