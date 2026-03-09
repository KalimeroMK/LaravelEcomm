@extends('front::layouts.master')

@section('title', 'Complaint Details')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('user.complaints.index') }}">My Complaints<i
                                        class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Complaint #{{ $complaint->id }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Complaint Details Section -->
    <section class="complaint-details section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Complaint #{{ $complaint->id }}</h2>
                        <span
                            class="badge badge-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'pending' ? 'warning' : 'info') }} badge-lg">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Number:</strong> {{ $complaint->order->order_number ?? $complaint->order->id }}
                            </p>
                            <p><strong>Order Date:</strong> {{ $complaint->order->created_at->format('M d, Y') }}</p>
                            <a href="{{ route('user.orders.detail', $complaint->order) }}" class="btn btn-sm btn-info">
                                View Order
                            </a>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Description</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $complaint->description }}</p>
                        </div>
                    </div>

                    @if ($complaint->complaint_replies->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Replies ({{ $complaint->complaint_replies->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($complaint->complaint_replies as $reply)
                                    <div class="reply-item mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $reply->user->name }}</strong>
                                            <small
                                                class="text-muted">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0 mt-2">{{ $reply->reply_content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.complaints.index') }}" class="btn btn-secondary">
                            <i class="ti-arrow-left"></i> Back to My Complaints
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Complaint Details Section -->
@endsection
