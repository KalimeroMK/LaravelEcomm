@if ($newsletter->exists)
    <form class="form-horizontal" method="POST" action="{{ route('newsletters.update', $newsletter->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('newsletters.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Email <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="email" placeholder="Enter email"
                           value="{{ $newsletter->email ?? null }}"
                           class="form-control">

                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="is_validated" class="form-control">
                        <option @checked($newsletter->is_validated === true) value="1">Active</option>
                        <option @checked($newsletter->is_validated === false)  value="2">Inactive</option>
                    </select>
                    @error('status')

                    @enderror
                </div>
                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
    </form>
