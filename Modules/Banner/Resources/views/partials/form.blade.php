<form class="form-horizontal" method="POST"
      action="{{ route($banner->exists ? 'banners.update' : 'banners.store', $banner->exists ? $banner->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($banner->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.title') <span
                class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
               value="{{ $banner->title ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="inputDesc" class="col-form-label">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{$banner->description ?? null }}</textarea>
    </div>

    <div class="form-group">
        <label for="inputPhoto" class="col-form-label">@lang('partials.image') <span
                class="text-danger">*</span></label>
        <div class="input-group">
            <span class="btn btn-round btn-rose btn-file">
                <span class="fileinput-new"></span>
                <input type="hidden" value="" name="images"><input type="file" name="images[]">
            </span>
        </div>
    </div>

    <div class="form-group">
        <label for="status" class="col-form-label">@lang('partials.status') <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="active">@lang('partials.active')</option>
            <option value="inactive">@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description', {
            versionCheck: false,
            filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
            filebrowserUploadMethod: 'form'
        });
    </script>
@endpush
