@extends('admin::layouts.master')
@section('title','E-SHOP || Payment Provider Page')
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
                @if(count($paymentProviders)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.public_key')</th>
                            <th>@lang('partials.secret_key')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.public_key')</th>
                            <th>@lang('partials.secret_key')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($paymentProviders as $paymentProvider)
                            <tr>
                                <td>{{$paymentProvider->id}}</td>
                                <td>{{$paymentProvider->name}}</td>
                                <td>{{$paymentProvider->public_key}}</td>
                                <td>{{$paymentProvider->secret_key}}</td>
                                <td>
                                    @if($paymentProvider->status==1)
                                        <span class="badge badge-success">@lang('partials.active')</span>
                                    @else
                                        <span class="badge badge-warning">@lang('partials.inactivee')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('payment_provider.edit',$paymentProvider->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
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