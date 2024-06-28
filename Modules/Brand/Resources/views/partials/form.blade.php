<form class="form-horizontal" method="POST"
      action="{{ route($brand->exists ? 'brands.update' : 'brands.store', $brand->exists ? $brand->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($brand->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.title') <span
                class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
               value="{{ $brand->title ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="status" class="col-form-label">@lang('partials.status') <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option @if($brand->status =="active") selected @endif value="active">@lang('partials.active')</option>
            <option @if($brand->status =="inactive") selected
                    @endif value="inactive">@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="form-group mb-3">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
