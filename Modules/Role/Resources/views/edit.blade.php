@extends('admin::layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="content" style="margin-top: 7%">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title "> {{trans('messages.role')}}</h4>
                    <p class="card-category"><a href="{{ route('home')}}">{{trans('messages.home')}}</a> -> <a
                                href="{{route('role.index')}}">{{trans('messages.role')}}</a></p>
                </div>
                <div class="card-body">
                    @include('role::partials.form')
                </div>
            </div>
        </div>
    </div>
@endsection