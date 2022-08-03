@extends('admin::layouts.master')

@section('title','E-SHOP || Tag edit')

@section('content')
    <div class="card">
        <h5 class="card-header">Edit tag</h5>
        <div class="card-body">
            @include('tag::partials.form')
        </div>
    </div>

@endsection

