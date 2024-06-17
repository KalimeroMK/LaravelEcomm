@extends('admin::layouts.master')
@section('title','E-SHOP || Brand Page')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
            <a href="{{route('attributes.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($attributes)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.code')</th>
                            <th>@lang('messages.type')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.code')</th>
                            <th>@lang('messages.type')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($attributes as $attribute)
                            <tr>
                                <td>{{$attribute->id}}</td>
                                <td>{{$attribute->name}}</td>
                                <td>{{$attribute->code}}</td>
                                <td>{{$attribute->type}}</td>
                                <td>
                                    <a href="{{route('attributes.edit',$attribute->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('attributes.destroy',$attribute->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$attribute->id}}" style="height:30px; width:30px;border-radius:50%
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


