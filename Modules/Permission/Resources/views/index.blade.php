@extends('admin::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="content">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title ">@lang('messages.permission')</h4>
                    <p class="card-category"><a href="{{ route('admin')}}">{{trans('messages.home')}}</a> -> <a
                                href="{{route('roles.index')}}">{{trans('messages.permission')}}</a></p>
                    <a href="{{route('permissions.create')}}" class="btn btn-primary btn-sm float-right"
                    <a href="{{route('permissions.create')}}" class="btn btn-primary btn-sm float-right"
                       data-toggle="tooltip"
                       data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('partials.name')</th>
                                <th>@lang('partials.action')</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>@lang('partials.name')</th>
                                <th>@lang('partials.action')</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td class="float-right">
                                        <a class="btn btn-primary"
                                           href="{{ route('permissions.edit',$permission->id) }}">@lang('partials.edit')</a>
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                              style="display:inline">
                                            @csrf
                                            <button class="btn btn-danger btn-sm dltBtn"
                                                    data-id="{{$permission->id}}" style="height:30px; width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                        class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
@endsection
