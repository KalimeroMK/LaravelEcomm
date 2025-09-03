@extends('admin::layouts.master')
@section('title', 'Email Campaign Analytics')
@section('content')
<div class="container-fluid">
    <h1>Email Campaign Analytics</h1>
    <p>Analytics data: {{ json_encode($analytics) }}</p>
</div>
@endsection
