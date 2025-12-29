@extends('admin::layouts.master')

@section('title', 'Queue Status')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Queue Status</h3>
                        <a href="{{ route('system.queue.failed') }}" class="btn btn-warning float-right">
                            View Failed Jobs ({{ $status['failed_jobs_count'] }})
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Queue Driver</th>
                                <td>{{ $status['driver'] }}</td>
                            </tr>
                            <tr>
                                <th>Connection</th>
                                <td>{{ $status['connection'] }}</td>
                            </tr>
                            <tr>
                                <th>Failed Jobs</th>
                                <td>
                                    <span class="badge badge-{{ $status['failed_jobs_count'] > 0 ? 'danger' : 'success' }}">
                                        {{ $status['failed_jobs_count'] }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

