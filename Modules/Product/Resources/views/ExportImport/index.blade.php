@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">@lang('partials.list')</h6>
            <a href="{{ route('product.index') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               title="Add User">
                <i class="fas fa-plus"></i>@lang('partials.list')
            </a>
        </div>

        <div class="card-body">
            <div class="card-body pt4">
                <!-- Nested Row within Card Body -->
                <div class="row text-center">
                    <div class="col-lg-12">
                        <form class=" tab-form"
                              action="{{ route('product.import') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-left">
                                        <a class="btn btn-info btn-sm"
                                           href="{{ route('product.export') }}">Products
                                            CSV Export</a>
                                    </div>
                                    <div class="text-right">
                                        <a class="btn btn-info btn-sm"
                                           href="{{ asset('backend/file/products.xlsx') }}"
                                           download="">Simple Csv Download</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-6">

                                    <div class="form-group position-relative ">
                                        <label for="file">Uplaod Your CSV File</label>
                                        <input type="file" accept="csv" class="form-control" name="file" id="file">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center">
                    <button type="submit" class="btn btn-secondary ">Submit</button>
                </div>


            </div>
        </div>
    </div>
@endsection

