@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Order Lists</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(!$orders->isEmpty())
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('sidebar.shipping')</th>
                            <th>@lang('partials.total_amount')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('sidebar.shipping')</th>
                            <th>@lang('partials.total_amount')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{$order->order_number}}</td>
                                <td>{{$order->user->name}}</td>
                                <td>{{$order->user->email}}</td>
                                <td>{{$order->quantity}}</td>
                                <td>{{ $order->shipping->type ??''}}</td>
                                <td>${{number_format($order->total_amount,2)}}</td>
                                <td>
                                    @if($order->status=='new')
                                        <span class="badge badge-primary">{{$order->status}}</span>
                                    @elseif($order->status=='process')
                                        <span class="badge badge-warning">{{$order->status}}</span>
                                    @elseif($order->status=='delivered')
                                        <span class="badge badge-success">{{$order->status}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$order->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('orders.show',$order->id)}}"
                                       class="btn btn-warning btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
                                    <a href="{{route('orders.edit',$order->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('orders.destroy',[$order->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$order->id}}" style="height:30px; width:30px;
                                                border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </form>
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
