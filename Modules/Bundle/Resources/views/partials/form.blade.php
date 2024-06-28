<form method="POST"
      action="{{ route($bundle->exists ? 'bundles.update' : 'bundles.store', $bundle->exists ? $bundle->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($bundle->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle">@lang('partials.name') <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
               value="{{ $bundle->name ?? '' }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="description">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{ $bundle->description ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="inputPrice">@lang('partials.price') <span class="text-danger">*</span></label>
        <input id="inputPrice" type="text" name="price" placeholder="Enter price" value="{{ $bundle->price ?? '' }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label for="products">@lang('partials.product')</label>
        <select class="form-control js-example-basic-multiple" id="products" name="product_id[]" multiple="multiple">
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="inputImage">@lang('partials.image') <span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
    </div>

    <div class="form-group">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button type="submit" class="btn btn-success">@lang('partials.submit')</button>
    </div>
</form>

<div class="row">
    @foreach($bundle->getMedia('bundle') as $media)
        <div class="col-md-3">
            <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
            <form action="{{ route('bundle.delete-media', ['modelId' => $bundle->id, 'mediaId' => $media->id]) }}"
                  method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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
