@extends('admin::layouts.master')
@section('title','E-SHOP || Complaints Page')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($complaints) > 0)
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('messages.users')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.replies_count')</th> {{-- New Replies Count Column --}}
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('messages.users')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.replies_count')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->id }}</td>
                                <td>{{ $complaint->user->name }}</td>
                                <td>{{ $complaint->order->order_number }}</td>
                                <td>
                                    <span class="badge {{ $complaint->status == 'active' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($complaint->status) }}
                                    </span>
                                </td>
                                <td>{{ $complaint->complaint_replies->count() }}</td> {{-- New Replies Count --}}
                                <td>
                                    <a href="{{ route('complaints.edit', $complaint->id) }}"
                                       class="btn btn-info btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="View" data-placement="bottom"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">@lang('partials.no_records_found')</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
