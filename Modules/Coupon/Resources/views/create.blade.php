@extends('admin::layouts.master')

@section('title','E-SHOP || Coupon Create')

@section('content')

    <div class="card">
        <h5 class="card-header">Add Coupon</h5>
        <div class="card-body">
            @include('coupon::partials.form')
        </div>
    </div>

@endsection

