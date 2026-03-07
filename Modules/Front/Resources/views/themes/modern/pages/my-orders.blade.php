@php
$activeTheme = 'modern';
$themePath = 'front::themes.' . $activeTheme;
@endphp
@extends($themePath . '.layouts.master')

@section('title','My Orders')

@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">My Orders</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">My Orders</h1>
                <div class="separator-2"></div>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('user.orders.detail', $order) }}">
                                                {{ $order->order_number ?? $order->id }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-sm btn-default">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <form action="{{ route('front.reorder', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-default">
                                                    <i class="fa fa-refresh"></i> Reorder
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-shopping-cart fa-3x mb-3"></i>
                        <h4>No orders found</h4>
                        <p>You haven't placed any orders yet.</p>
                        <a href="{{ route('front.product-grids') }}" class="btn btn-default">
                            Start Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
