@extends('admin::layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Notification</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Notifiable type</th>
                                    <th>Data</th>
                                    <th>Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$notification->type ?? 'No title'}}</td>
                                    <td>{{$notification->notifiable_type ?? 'No message'}}</td>
                                    <td>{{$notification->data ?? 'No message'}}</td>
                                    <td>{{$notification->created_at->format('F d, Y h:i A')}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
