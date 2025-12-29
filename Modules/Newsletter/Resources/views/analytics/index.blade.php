@extends('admin::layouts.master')

@section('title', 'Newsletter Analytics')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Newsletter Analytics</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Emails Sent</h5>
                                        <h2 class="text-primary">{{ $analytics['total_sent'] ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Opened</h5>
                                        <h2 class="text-success">{{ $analytics['total_opened'] ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Clicked</h5>
                                        <h2 class="text-info">{{ $analytics['total_clicked'] ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Open Rate</h5>
                                        <h2 class="text-warning">{{ number_format($analytics['open_rate'] ?? 0, 2) }}%</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Subscriber Statistics</h4>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Total Subscribers:</strong> {{ $subscriberStats['total_subscribers'] ?? 0 }}</p>
                                        <p><strong>Validated Subscribers:</strong> {{ $subscriberStats['validated_subscribers'] ?? 0 }}</p>
                                        <p><strong>New This Month:</strong> {{ $subscriberStats['new_this_month'] ?? 0 }}</p>
                                        <p><strong>Unsubscribed This Month:</strong> {{ $subscriberStats['unsubscribed_this_month'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Performance Metrics</h4>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Click Rate:</strong> {{ number_format($analytics['click_rate'] ?? 0, 2) }}%</p>
                                        <p><strong>Bounce Rate:</strong> {{ number_format($analytics['bounce_rate'] ?? 0, 2) }}%</p>
                                        <p><strong>Unsubscribe Rate:</strong> {{ number_format($analytics['unsubscribe_rate'] ?? 0, 2) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

