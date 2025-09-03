@extends('admin::layouts.master')
@section('title','E-SHOP || Email Campaigns')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Email Campaigns</h6>
            <a href="{{route('admin.email-campaigns.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Create Campaign">
                <i class="fas fa-plus"></i> Create Campaign
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $templates->count() }}</h4>
                            <p>Available Templates</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ $subscribers }}</h4>
                            <p>Active Subscribers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>0</h4>
                            <p>Campaigns Sent</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>0%</h4>
                            <p>Average Open Rate</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5>Quick Actions</h5>
                    <div class="list-group">
                        <a href="{{route('admin.email-campaigns.create')}}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus text-primary"></i> Create New Campaign
                        </a>
                        <a href="{{route('admin.email-templates.index')}}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit text-info"></i> Manage Templates
                        </a>
                        <a href="{{route('admin.email-campaigns.analytics')}}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar text-success"></i> View Analytics
                        </a>
                        <a href="{{route('newsletters.index')}}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users text-warning"></i> Manage Subscribers
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Recent Templates</h5>
                    @if($templates->count() > 0)
                        <div class="list-group">
                            @foreach($templates->take(5) as $template)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $template->name }}</h6>
                                        <small>{{ $template->template_type }}</small>
                                    </div>
                                    <p class="mb-1">{{ $template->subject }}</p>
                                    <small>
                                        @if($template->is_default)
                                            <span class="badge badge-warning">Default</span>
                                        @endif
                                        @if($template->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No templates available. <a href="{{route('admin.email-templates.create')}}">Create your first template</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #dee2e6;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush

