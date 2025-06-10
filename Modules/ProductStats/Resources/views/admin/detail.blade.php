@extends('admin::layouts.master')
@section('title', 'Product Detailed Stats')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Product Stats: {{ $product->title }}</h1>
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label>From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label>To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    <div class="card shadow mb-4">
        <div class="card-body">
            <ul class="list-group mb-4">
                <li class="list-group-item">Impressions: <b>{{ $stats['impressions'] }}</b></li>
                <li class="list-group-item">Clicks: <b>{{ $stats['clicks'] }}</b></li>
                <li class="list-group-item">CTR: <b>{{ $stats['ctr'] * 100 }}%</b></li>
            </ul>
            <h5>Impressions (last 30, filtered):</h5>
            <ul class="list-group mb-4">
                @foreach($impressions as $impression)
                    <li class="list-group-item">{{ $impression->created_at }} | IP: {{ $impression->ip_address }} | User: {{ $impression->user_id ?? '-' }}</li>
                @endforeach
            </ul>
            <h5>Clicks (last 30, filtered):</h5>
            <ul class="list-group">
                @foreach($clicks as $click)
                    <li class="list-group-item">{{ $click->created_at }} | IP: {{ $click->ip_address }} | User: {{ $click->user_id ?? '-' }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <a href="{{ route('product-stats.index') }}" class="btn btn-secondary">Back to Product Stats</a>
</div>
@endsection
