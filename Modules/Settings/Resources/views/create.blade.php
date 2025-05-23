@extends('admin::layouts.master')

@section('title','E-SHOP || Banner Create')

@section('content')

    <div class="card">
        <h5 class="card-header">@lang('partials.create')</h5>
        <div class="card-body">
            @include('settings::partials.form', ['settings' => $settings ?? []])
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
@endpush
