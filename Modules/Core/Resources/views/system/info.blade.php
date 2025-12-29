@extends('admin::layouts.master')

@section('title', 'System Information')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>PHP Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Version</th>
                                        <td>{{ $info['php']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Memory Limit</th>
                                        <td>{{ $info['php']['memory_limit'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Max Execution Time</th>
                                        <td>{{ $info['php']['max_execution_time'] }}s</td>
                                    </tr>
                                    <tr>
                                        <th>Upload Max Filesize</th>
                                        <td>{{ $info['php']['upload_max_filesize'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Laravel Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Version</th>
                                        <td>{{ $info['laravel']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Environment</th>
                                        <td>{{ $info['laravel']['environment'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Debug Mode</th>
                                        <td>{{ $info['laravel']['debug'] ? 'Enabled' : 'Disabled' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Timezone</th>
                                        <td>{{ $info['laravel']['timezone'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Database</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Driver</th>
                                        <td>{{ $info['database']['driver'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Database</th>
                                        <td>{{ $info['database']['connection'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Cache & Queue</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Cache Driver</th>
                                        <td>{{ $info['cache']['driver'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Queue Driver</th>
                                        <td>{{ $info['queue']['driver'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Queue Connection</th>
                                        <td>{{ $info['queue']['connection'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Server Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Operating System</th>
                                        <td>{{ $info['server']['os'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Server Software</th>
                                        <td>{{ $info['server']['server_software'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

