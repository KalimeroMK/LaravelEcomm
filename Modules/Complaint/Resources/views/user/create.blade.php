@extends('front::layouts.master')

@section('title', 'File a Complaint')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('user.orders.history') }}">My Orders<i class="ti-arrow-right"></i></a>
                            </li>
                            <li class="active"><a href="javascript:void(0)">File Complaint</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- File Complaint Section -->
    <section class="file-complaint section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">File a Complaint</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Number:</strong> {{ $order->order_number ?? $order->id }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                            <p><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Complaint Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.complaints.store', $order) }}" method="POST">
                                @csrf

                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control" rows="6" required
                                        placeholder="Please provide detailed information about your complaint">{{ old('description') }}</textarea>
                                    <small class="form-text text-muted">Please include all relevant details to help us
                                        resolve your issue quickly.</small>
                                </div>

                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                                    <a href="{{ route('user.orders.history') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End File Complaint Section -->
@endsection
