@extends('admin::layouts.master')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('partials.create')</h5>
        <div class="card-body">
            @include('product::partials.form', [
                'product' => $product ?? [],
                'categories' => $categories ?? [],
                'tags' => $tags ?? [],
                'brands' => $brands ?? [],
                'attributes' => $attributes ?? [],
            ])
        </div>
    </div>
@endsection

@push('styles')
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
@endpush
