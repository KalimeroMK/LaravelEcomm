@extends('admin::layouts.master')
@section('content')

    <div class="container-fluid">
        <div class="content">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title "> {{trans('sidebar.category')}}</h4>
                    <p class="card-category"><a href="{{ route('admin')}}">{{trans('messages.home')}}</a> -> <a
                                href="{{route('category.index')}}">{{trans('sidebar.category')}}</a></p>
                </div>
                <div class="card-body">
                    @include('category::partials.form')
                </div>
            </div>
        </div>
    </div>
@stop
