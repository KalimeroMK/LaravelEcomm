@extends('admin::layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="content" style="margin-top: 7%">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title "> {{trans('messages.permission')}}</h4>
                    <p class="card-category"><a href="{{ route('admin')}}">{{trans('messages.home')}}</a> -> <a
                                href="{{route('permissions.index')}}">{{trans('messages.permission')}}</a></p>
                </div>
                <div class="card-body">
                    @include('permission::partials.form')
                </div>
            </div>
        </div>
    </div>
@endsection

