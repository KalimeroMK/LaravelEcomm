@extends('admin::layouts.master')

@section('title', 'Failed Jobs')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Failed Jobs</h3>
                        <div class="float-right">
                            <form action="{{ route('system.queue.retry-all') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Retry all failed jobs?')">
                                    Retry All
                                </button>
                            </form>
                            <a href="{{ route('system.queue') }}" class="btn btn-secondary">Back to Queue</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Connection</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>{{ $job->connection }}</td>
                                        <td>{{ $job->queue }}</td>
                                        <td>{{ $job->failed_at }}</td>
                                        <td>
                                            <form action="{{ route('system.queue.retry', $job->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Retry</button>
                                            </form>
                                            <form action="{{ route('system.queue.delete', $job->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this failed job?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No failed jobs</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $jobs->links('pagination::admin-bootstrap-5') }}
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

