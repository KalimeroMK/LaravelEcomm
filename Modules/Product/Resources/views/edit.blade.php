@extends('admin::layouts.master')

@section('title','E-SHOP || Product Create')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('partials.edit')</h5>
        <div class="card-body">
            @include('product::partials.form')
        </div>
    </div>

@endsection


@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"
          rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <!-- select2 -->
    <script type="text/javascript">
        $('#category').select2().val({!! json_encode($product->categories()->allRelatedIds()) !!}).trigger('change');
        $('#size').select2().val({!! json_encode($product->sizes()->allRelatedIds()) !!}).trigger('change');
        $('#tag').select2().val({!! json_encode($product->tags()->allRelatedIds()) !!}).trigger('change');
    </script>
@endpush
