@extends('admin::layouts.master')

@section('title','E-SHOP || Banner Create')

@section('content')
    <div class="card">
        <h5 class="card-header">Edit Banner</h5>
        <div class="card-body">
            @include('settings::partials.form')
        </div>
    </div>

@endsection

