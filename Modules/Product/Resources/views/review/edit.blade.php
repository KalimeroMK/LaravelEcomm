@extends('admin::layouts.master')

@section('title','Review Edit')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('partials.edit')</h5>
        <div class="card-body">
            <form action="{{route('reviews.update',$review->id)}}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="name">@lang('partials.review_by'):</label>
                    <input type="text" disabled class="form-control" value="{{$review->user->name}}">
                </div>
                <div class="form-group">
                    <label for="review">@lang('partials.review')</label>
                    <textarea name="review" id="" cols="20" rows="10"
                              class="form-control">{{$review->review}}</textarea>
                </div>
                <div class="form-group">
                    <label for="status">@lang('partials.status') :</label>
                    <select name="status" id="" class="form-control">
                        <option value="">--Select Status--</option>
                        <option value="active" {{(($review->status=='active')? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($review->status=='inactive')? 'selected' : '')}}>Inactive
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