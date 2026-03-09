@extends('front::layouts.master')

@section('title', 'My Complaints')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">My Complaints</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- My Complaints Section -->
    <section class="my-complaints section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">My Complaints</h2>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($complaints->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Complaint #</th>
                                        <th>Order #</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($complaints as $complaint)
                                        <tr>
                                            <td>#{{ $complaint->id }}</td>
                                            <td>{{ $complaint->order->order_number ?? $complaint->order->id }}</td>
                                            <td>{{ Str::limit($complaint->description, 50) }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($complaint->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('user.complaints.show', $complaint) }}"
                                                    class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti-comment-alt fa-3x text-muted mb-3"></i>
                            <h5>No complaints found</h5>
                            <p class="text-muted">You haven't filed any complaints yet.</p>
                            <a href="{{ route('user.orders.history') }}" class="btn btn-primary">
                                View My Orders
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- End My Complaints Section -->
@endsection
