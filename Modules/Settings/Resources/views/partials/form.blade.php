<form class="form-horizontal" method="POST" action="{{ route('settings.update', $settings->id) }}"
      enctype="multipart/form-data">
    @method('put')
    @csrf
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">Short info <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="short_des" placeholder="Short description"
               value="{{ $settings->short_des ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.email') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="email" placeholder="Short description"
               value="{{ $settings->email ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.phone') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="phone" placeholder="Short description"
               value="{{ $settings->phone ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('messages.address') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="address" placeholder="address"
               value="{{ $settings->address ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputDesc" class="col-form-label">@lang('partials.description')</label>
        <textarea class="form-control" id="description"
                  name="description">{{$settings->description ?? null }}</textarea>

    </div>
    <div class="form-group">
        <label for="inputPhoto" class="col-form-label">@lang('partials.logo') <span class="text-danger">*</span></label>
        <div class="input-group">
              <span class="btn btn-round btn-rose btn-file">
                    <span class="fileinput-new"></span>
                    <input type="hidden" value="" name="logo"><input type="file"
                                                                     name="logo">
              </span>
        </div>

        <div class="form-group mb-3">
            <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
            <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
        </div>
    </div>
</form>
