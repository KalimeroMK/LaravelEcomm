@extends('admin::layouts.master')

@section('title', 'System Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2>System Management</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Health Check</h5>
                        <a href="{{ route('system.health') }}" class="btn btn-primary" target="_blank">Check Health</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>System Info</h5>
                        <a href="{{ route('system.info') }}" class="btn btn-info">View Info</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Log Files</h5>
                        <a href="{{ route('system.logs') }}" class="btn btn-warning">View Logs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Queue Status</h5>
                        <a href="{{ route('system.queue') }}" class="btn btn-secondary">View Queue</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Maintenance Mode</h5>
                    </div>
                    <div class="card-body">
                        @if(app()->isDownForMaintenance())
                            <p class="text-warning">Maintenance mode is currently enabled</p>
                            <form action="{{ route('system.maintenance.disable') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Disable Maintenance Mode</button>
                            </form>
                        @else
                            <p class="text-success">Maintenance mode is disabled</p>
                            <form action="{{ route('system.maintenance.enable') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning">Enable Maintenance Mode</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Cache Management</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('system.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Clear Cache</button>
                        </form>
                        <form action="{{ route('system.cache.config.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info">Clear Config</button>
                        </form>
                        <form action="{{ route('system.cache.route.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info">Clear Route</button>
                        </form>
                        <form action="{{ route('system.cache.view.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info">Clear View</button>
                        </form>
                        <form action="{{ route('system.cache.all.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Clear All</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Database Backup</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('system.backup') }}" class="btn btn-primary">Create Backup</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Environment Variables</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('system.environment') }}" class="btn btn-secondary">View Environment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

