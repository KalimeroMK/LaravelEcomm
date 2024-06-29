@extends('admin::layouts.master')
@section('title','E-SHOP || Tenant Page')
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
            <a href="{{route('tenant.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($tenants)>0)
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.domain')</th>
                            <th>@lang('partials.database')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.domain')</th>
                            <th>@lang('partials.database')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td>{{$tenant->id}}</td>
                                <td>{{$tenant->name}}</td>
                                <td>{{$tenant->domain}}</td>
                                <td>{{$tenant->database}}</td>
                                <td>
                                    <a href="{{route('tenant.edit',$tenant->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('tenant.destroy',[$tenant->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$tenant->id}}" style="height:30px; width:30px;border-radius:50%
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


