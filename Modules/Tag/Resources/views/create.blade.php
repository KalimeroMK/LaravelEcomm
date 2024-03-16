@extends('admin::layouts.master')

@section('title','E-SHOP || Tag Create')

@section('content')

    <div class="card">
        <h5 class="card-header">@lang('partials.create')</h5>
        <div class="card-body">
            @include('tag::partials.form')
        </div>
    </div>

@endsection
