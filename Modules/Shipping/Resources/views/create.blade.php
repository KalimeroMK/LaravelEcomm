@extends('admin::layouts.master')

@section('content')

    <div class="card">
        <h5 class="card-header">@lang('partials.create')</h5>
        <div class="card-body">
            <form method="post" action="{{route('shipping.store')}}">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">@lang('partials.type') <span
                                class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="type" placeholder="@lang('partials.title')"
                           value="{{old('type')}}"
                           class="form-control">
                    @error('type')

                    @enderror
                </div>

                <div class="form-group">
                    <label for="price" class="col-form-label">@lang('partials.price') <span class="text-danger">*</span></label>
                    <input id="price" type="number" name="price" placeholder="Enter price" value="{{old('price')}}"
                           class="form-control">

                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">@lang('partials.status') <span
                                class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                </div>
                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                    <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
                </div>
            </form>
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
        $('#lfm').filemanager('image');

        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
@endpush