@extends('admin::layouts.master')

@section('title', 'View Log: ' . $filename)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Log File: {{ $filename }}</h3>
                        <a href="{{ route('system.logs') }}" class="btn btn-secondary float-right">Back to Logs</a>
                    </div>
                    <div class="card-body">
                        <pre style="max-height: 600px; overflow-y: auto; background: #f4f4f4; padding: 15px; border-radius: 4px;">@foreach($lines as $line){{ $line }}
@endforeach</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

