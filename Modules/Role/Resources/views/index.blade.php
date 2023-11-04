@extends('admin::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="content" style="margin-top: 7%">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title "> {{trans('messages.pages')}}</h4>
                    <p class="card-category"><a href="{{ route('home')}}">{{trans('messages.home')}}</a> -> <a
                                href="{{route('role.index')}}">{{trans('messages.role')}}</a></p>
                    <a href="{{route('role.create')}}" class="btn btn-primary btn-sm float-right"
                       data-toggle="tooltip"
                       data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add role</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td class="float-right">
                                        <a class="btn btn-primary"
                                           href="{{ route('role.edit',$role->id) }}">Edit</a>
                                        <form action="{{ route('role.destroy', $role->id) }}" method="POST"
                                              style="display:inline">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm dltBtn"
                                                    data-id="{{$role->id}}" style="height:30px; width:30px;border-radius:50%
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
