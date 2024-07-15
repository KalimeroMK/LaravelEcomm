@extends('openai::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('openai.name') !!}</p>
@endsection
