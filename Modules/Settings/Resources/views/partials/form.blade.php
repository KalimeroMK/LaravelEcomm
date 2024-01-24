<form class="form-horizontal" method="POST" action="{{ route('setting.update', $settings->id) }}"
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
        <label for="inputTitle" class="col-form-label">Email <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="email" placeholder="Short description"
               value="{{ $settings->email ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">Phone <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="phone" placeholder="Short description"
               value="{{ $settings->phone ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">Address <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="address" placeholder="Short description"
               value="{{ $settings->address ?? null }}"
               class="form-control">

    </div>
    <div class="form-group">
        <label for="inputDesc" class="col-form-label">Description</label>
        <textarea class="form-control" id="description"
                  name="description">{{$settings->description ?? null }}</textarea>

    </div>
    <div class="form-group">
        <label for="inputPhoto" class="col-form-label">Logo <span class="text-danger">*</span></label>
        <div class="input-group">
              <span class="btn btn-round btn-rose btn-file">
                    <span class="fileinput-new"></span>
                    <input type="hidden" value="" name="logo"><input type="file"
                                                                     name="logo">
              </span>
        </div>

        <div class="form-group mb-3">
            <button type="reset" class="btn btn-warning">Reset</button>
            <button class="btn btn-success" type="submit">Submit</button>
        </div>
    </div>
</form>
