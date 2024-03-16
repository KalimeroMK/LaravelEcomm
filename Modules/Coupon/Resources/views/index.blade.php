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
            <h6 class="m-0 font-weight-bold text-primary float-left">Coupon List</h6>
            <a href="{{route('coupon.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Coupon</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($coupons)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.code')</th>
                            <th>@lang('partials.type')</th>
                            <th>@lang('partials.value')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.code')</th>
                            <th>@lang('partials.type')</th>
                            <th>@lang('partials.value')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td>{{$coupon->id}}</td>
                                <td>{{$coupon->code}}</td>
                                <td>
                                    @if($coupon->type=='fixed')
                                        <span class="badge badge-primary">{{$coupon->type}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$coupon->type}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->type=='fixed')
                                        ${{number_format($coupon->value,2)}}
                                    @else
                                        {{$coupon->value}}%
                                    @endif</td>
                                <td>
                                    @if($coupon->status=='active')
                                        <span class="badge badge-success">{{$coupon->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$coupon->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('coupon.edit',$coupon->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('coupon.destroy',[$coupon->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$coupon->id}}" style="height:30px; width:30px;border-radius:50%
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

