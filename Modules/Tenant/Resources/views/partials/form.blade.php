<form class="form-horizontal" method="POST"
      action="{{ route($tenant->exists ? 'tenant.update' : 'banners.store', $tenant->exists ? $tenant->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($tenant->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.name') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
               value="{{ $tenant->name ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="inputDesc" class="col-form-label">@lang('partials.domain')</label>
        <input id="inputTitle" type="text" name="domain" placeholder="@lang('partials.domain')"
               value="{{ $tenant->domain ?? null }}" class="form-control"></div>

    <div class="form-group">
        <label for="inputDesc" class="col-form-label">@lang('partials.database')</label>
        <input id="inputTitle" type="text" name="database" placeholder="@lang('partials.database')"
               value="{{ $tenant->database ?? null }}" class="form-control"></div>

    <div class="form-group mb-3">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
