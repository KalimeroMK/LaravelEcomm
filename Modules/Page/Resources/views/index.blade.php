@extends('page::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('page.name') !!}</p>
@endsection
