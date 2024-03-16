@extends('admin::layouts.master')

@section('title','Comment Edit')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('partials.edit')</h5>
        <div class="card-body">
            <form action="{{route('comments.update',$comment->id)}}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="name">@lang('partials.author'):</label>
                    <input type="text" disabled class="form-control" value="{{$comment->user_info->name}}">
                </div>
                <div class="form-group">
                    <label for="comment">@lang('sidebar.comments')</label>
                    <textarea name="comment" id="" cols="20" rows="10"
                              class="form-control">{{$comment->comment}}</textarea>
                </div>
                <div class="form-group">
                    <label for="status">@lang('partials.status') :</label>
                    <select name="status" id="" class="form-control">
                        <option value="">--Select Status--</option>
                        <option value="active" {{(($comment->status=='active')? 'selected' : '')}}>@lang('partials.active')
                        </option>
                        <option value="inactive" {{(($comment->status=='inactive')? 'selected' : '')}}>@lang('partials.inactive')
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-info, .shipping-info {
            background: #ECECEC;
            padding: 20px;
        }

        .order-info h4, .shipping-info h4 {
            text-decoration: underline;
        }
    </style>
@endpush