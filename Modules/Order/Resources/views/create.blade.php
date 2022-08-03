@extends('admin::layouts.master')

@section('title','E-SHOP || Banner Create')

@section('content')

    <div class="card">
        <h5 class="card-header">Add order</h5>
        <div class="card-body">
            @include('order::partials.form')
        </div>
    </div>

@endsection

