<form class="form-horizontal" method="POST"
      action="{{ route($paymentProvider->exists ? 'payment_provider.update' : 'payment_provider.store',
      $paymentProvider->exists ? $paymentProvider->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($paymentProvider->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.name') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
               value="{{ $paymentProvider->name ?? null }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.public_key') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="public_key" placeholder="@lang('partials.public_key')"
               value="{{ $paymentProvider->public_key ?? null }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.secret_key') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="secret_key" placeholder="@lang('partials.public_key')"
               value="{{ $paymentProvider->secret_key ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="status" class="col-form-label">@lang('partials.status') <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="1" {{ $paymentProvider->status == 1 ? 'selected' : '' }}>@lang('partials.active')</option>
            <option value="0" {{ $paymentProvider->status == 0 ? 'selected' : '' }}>@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
