@extends('admin::layouts.master')
@section('title','Product Edit')
@section('content')
    <div class="container-fluid">
        <form method="POST"
              action="{{ route($product->exists ? 'products.update' : 'products.store', $product->exists ? $product->id : null) }}"
              enctype="multipart/form-data">
            @csrf
            @if($product->exists)
                @method('put')
            @endif

            <div class="form-group">
                <label for="inputTitle">@lang('partials.title')</label>
                <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
                       value="{{ $product->title ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="summary">@lang('partials.summary')</label>
                <textarea class="form-control" id="summary" name="summary">{{$product->summary ?? ''}}</textarea>
            </div>

            <div class="form-group">
                <label for="description">@lang('partials.description')</label>
                <textarea class="form-control" id="description"
                          name="description">{{ $product->description ??''}}</textarea>
            </div>

            <div class="form-group">
                <label for="inputImage">@lang('partials.image')</label>
                <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
            </div>

            <div class="form-group">
                <label for="price">@lang('partials.price')</label>
                <input id="price" type="number" name="price" placeholder="Enter price"
                       value="{{ $product->price ?? '' }}"
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control" required>
                    <option
                        value="active" {{ (isset($product->status) && $product->status == 'active') ? 'selected' : '' }}>
                        Active
                    </option>
                    <option
                        value="inactive" {{ (isset($product->status) && $product->status == 'inactive') ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_featured">@lang('partials.is_featured')</label><br>
                <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
            </div>

            <div class="form-group">
                <label for="cat_id">@lang('partials.categories')</label>
                <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                        multiple="multiple">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>

            <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
            <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
        </form>
    </div>
    <div class="row">
        @foreach($product->getMedia('product') as $media)
            <div class="col-md-3">
                <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
                <form action="{{ route('product.delete-media', ['modelId' => $product->id, 'mediaId' => $media->id]) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('description', {
                filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
                filebrowserUploadMethod: 'form'
            });
        </script>
    @endpush
@endsection
