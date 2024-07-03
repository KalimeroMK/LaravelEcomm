<form method="POST"
      action="{{ route($newsletter->exists ? 'newsletters.update' : 'newsletters.store', $newsletter->exists ? $newsletter->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($newsletter->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputEmail">Email <span class="text-danger">*</span></label>
        <input id="inputEmail" type="text" name="email" placeholder="@lang('partials.email')"
               value="{{ $newsletter->email ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="is_validated" class="form-control">
            <option value="1" {{ $newsletter->is_validated ? 'selected' : '' }}>@lang('partials.active')</option>
            <option value="2" {{ !$newsletter->is_validated ? 'selected' : '' }}>@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
