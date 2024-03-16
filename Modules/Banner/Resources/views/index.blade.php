@extends('admin::layouts.master')
@section('title','E-SHOP || Banner Page')
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
            <a href="{{route('banners.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($banners)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('partials.slug')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('partials.slug')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($banners as $banner)
                            <tr>
                                <td>{{$banner->id}}</td>
                                <td>{{$banner->title}}</td>
                                <td>{{$banner->slug}}</td>
                                <td>
                                    @if($banner->photo)
                                        <img src="{{$banner->photo}}" class="img-fluid zoom" style="max-width:80px"
                                             alt="{{$banner->photo}}">
                                    @else
                                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid zoom"
                                             style="max-width:100%" alt="avatar.png">
                                    @endif
                                </td>
                                <td>
                                    @if($banner->status=='active')
                                        <span class="badge badge-success">{{$banner->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$banner->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('banners.edit',$banner->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('banners.destroy',[$banner->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$banner->id}}" style="height:30px; width:30px;border-radius:50%
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



