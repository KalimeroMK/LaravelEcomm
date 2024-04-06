@extends('admin::layouts.master')
@section('title','E-SHOP || All notifications')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Newsletter List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($notifications))
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>Time</th>
                            <th>@lang('partials.title')</th>
                            <th>Url</th>
                            <th>@lang('partials.type')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>Time</th>
                            <th>@lang('partials.title')</th>
                            <th>Url</th>
                            <th>@lang('partials.type')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach ( $notifications as $notification)

                            <tr>
                                <td>{{$loop->index +1}}</td>
                                <td>{{$notification->created_at->format('F d, Y h:i A')}}</td>
                                @php
                                    $items = json_decode($notification->data, true) ?? [];
                                @endphp
                                @foreach($items as $key => $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                                <td>
                                    <a href="{{route('admin.notification', $notification->id) }}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="view"
                                       data-placement="bottom"><i class="fas fa-eye"></i></a>
                                    <form method="POST" action="{{ route('notification.delete', $notification->id) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$notification->id}}" style="height:30px;
                                                width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No brands found!!! Please create brand</h6>
                @endif
            </div>
        </div>
    </div>
@endsection


