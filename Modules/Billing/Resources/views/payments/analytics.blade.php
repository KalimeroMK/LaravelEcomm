@extends('admin::layouts.master')

@section('title', 'Payment Analytics')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Total Revenue') }}</h5>
                        <h2 class="text-success">${{ number_format($analytics['total_amount'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Total Payments') }}</h5>
                        <h2 class="text-info">{{ $analytics['total_count'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Pending Payments') }}</h5>
                        <h2 class="text-warning">{{ $analytics['pending_count'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Success Rate') }}</h5>
                        <h2 class="text-primary">{{ number_format($analytics['success_rate'], 2) }}%</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Statistics</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('Failed Payments') }}:</strong> {{ $analytics['failed_count'] }}</p>
                        <p><strong>{{ __('Success Rate') }}:</strong> {{ number_format($analytics['success_rate'], 2) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

