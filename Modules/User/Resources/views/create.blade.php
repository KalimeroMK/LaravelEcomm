@extends('admin::layouts.master')

@section('title','Admin Profile')

@section('content')

    <div class="container-fluid">
        <div class="content">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title "> {{trans('messages.dashboard')}}</h4>
                    <p class="card-category"><a href="{{ route('admin')}}">{{trans('messages.dashboard')}}</a>
                        ->
                        <a
                            href="{{route('users.index')}}">{{trans('messages.users')}}</a></p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @include('user::partials.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
