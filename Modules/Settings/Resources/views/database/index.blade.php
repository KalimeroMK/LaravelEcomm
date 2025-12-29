@extends('admin::layouts.master')

@section('title', 'Database Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Database Management</h3>
                        <small class="text-danger">⚠️ Warning: These operations can affect your database. Use with caution.</small>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Migrations</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('settings.database.migrate') }}" method="POST" class="mb-3">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-block" 
                                                    onclick="return confirm('Run pending migrations?')">
                                                Run Migrations
                                            </button>
                                        </form>

                                        <form action="{{ route('settings.database.migrate-fresh') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-block" 
                                                    onclick="return confirm('⚠️ WARNING: This will drop all tables and re-run migrations. Are you absolutely sure?')">
                                                Fresh Migrations (Drop All Tables)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Seeders</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('settings.database.seed') }}" method="POST" class="mb-3">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-block" 
                                                    onclick="return confirm('Run database seeders?')">
                                                Run Seeders
                                            </button>
                                        </form>

                                        <form action="{{ route('settings.database.migrate-fresh-seed') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-block" 
                                                    onclick="return confirm('⚠️ WARNING: This will drop all tables, re-run migrations, and seed the database. Are you absolutely sure?')">
                                                Fresh Migrations + Seeders
                                            </button>
                                        </form>
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

