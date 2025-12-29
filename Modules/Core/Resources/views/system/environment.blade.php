@extends('admin::layouts.master')

@section('title', 'Environment Variables')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Environment Variables</h3>
                        <small class="text-muted">Sensitive values are masked for security</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($envVars as $key => $value)
                                    <tr>
                                        <td><code>{{ $key }}</code></td>
                                        <td><code>{{ $value }}</code></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No environment variables found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

