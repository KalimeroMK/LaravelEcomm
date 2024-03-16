@extends('admin::layouts.master')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
            <a href="{{route('category.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="border-bottom title-part-padding">
                            <a href="{{ route('category.order') }}" class="btn btn-default">Add
                                Category</a>
                        </div>
                        <div class="card-body">
                            {!! $categories !!}
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jquery.nestable.js') }}"></script>
    <script>
        $(document).ready(function () {

            const updateOutput = function (e) {
                const list = e.length ? e : $(e.target),
                    output = list.data("output");
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable("serialize"))); //, null, 2));
                } else {
                    output.val("JSON browser support required for this demo.");
                }


                const order = $('.dd').nestable('serialize');


                // Make an AJAX call to update the order on the backend
                $.ajax({
                    type: 'POST',
                    url: '{{ route('category.order') }}',
                    data: {order: order},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': "{{ @csrf_token() }}"
                    },
                    success: function (response) {
                        console.log(response); // Handle the success response
                    },
                    error: function (error) {
                        console.log(error); // Handle the error
                    }
                });
            };

            $("#nestable")
                .nestable({
                    group: 1,
                })
                .on("change", updateOutput);

            updateOutput($("#nestable").data("output", $("#nestable-output")));


        });
    </script>
@endpush
@push('styles')
    <link href="{{asset('css/nestable.css')}}" rel="stylesheet">
    <style>
        .dd-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-category-icon {
            margin-left: auto;
            padding: 0 10px;
        }

        .edit-category-icon i {
            font-size: 1.2em;
        }
    </style>
@endpush
