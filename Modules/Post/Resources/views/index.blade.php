@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Post Lists</h6>
            <a href="{{route('posts.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a><br><br>

            <form action="{{ route('posts.import') }}" method="POST" enctype="multipart/form-data"
                  class="col-3 float-right">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-primary btn-sm">@lang('messages.import')</button>
                <a href="{{route('posts.export')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                   data-placement="bottom" title="Export posts"><i class="fas fa-plus"></i>@lang('partials.export')</a>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($posts)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('sidebar.category')</th>
                            <th>@lang('partials.description')</th>
                            <th>@lang('partials.tags')</th>
                            <th>@lang('partials.author')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('sidebar.category')</th>
                            <th>@lang('partials.description')</th>
                            <th>@lang('partials.tags')</th>
                            <th>@lang('partials.author')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach($posts as $post)
                            <tr>
                                <td>{{$post->id}}</td>
                                <td>{{$post->title}}</td>
                                <td> @foreach($post->categories as $category)
                                        {{$category->title}}
                                    @endforeach
                                </td>
                                <td>{{$post->tags}}</td>
                                <td>{{$post->description}}</td>
                                <td>{{ $post->author_info->name}}</td>
                                <td>
                                    @if($post->photo)
                                        <img src="{{$post->photo}}" class="img-fluid zoom" style="max-width:80px"
                                             alt="{{$post->photo}}">
                                    @else
                                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid"
                                             style="max-width:80px" alt="avatar.png">
                                    @endif
                                </td>
                                <td>
                                    @if($post->status=='active')
                                        <span class="badge badge-success">{{$post->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$post->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('posts.edit',$post->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('posts.destroy',[$post->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$post->id}}" style="height:30px; width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No posts found!!! Please create Post</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

