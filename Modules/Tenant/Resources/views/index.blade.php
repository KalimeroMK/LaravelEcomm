@extends('tenant::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('tenant.name') !!}</p>
@endsection
