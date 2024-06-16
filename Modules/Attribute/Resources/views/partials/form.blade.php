<form class="form-horizontal" method="POST"
      action="{{ route($attribute->exists ? 'attributes.update' : 'attributes.store', $attribute->exists ? $attribute->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($attribute->exists)
        @method('put')
    @endif

    <div class="form-group row">
        <div class="col-6">
            @foreach(['name', 'code'] as $field)
                <label for="inputTitle" class="col-form-label">@lang("partials.$field") <span
                        class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="{{ $field }}" placeholder="@lang("partials.$field")"
                       value="{{ $attribute->$field ?? null }}" class="form-control">
            @endforeach
        </div>
        <div class="col-6">
            @foreach(['filterable', 'configurable'] as $field)
                <div class="form-group">
                    <label for="status" class="col-form-label">@lang("partials.$field") <span
                            class="text-danger">*</span></label>
                    <select name="{{ $field }}" class="form-control">
                        <option
                            @selected(old($field, $attribute->$field === 1)) value="1">@lang('partials.yes')</option>
                        <option @selected(old($field, $attribute->$field === 0)) value="0">@lang('partials.no')</option>
                    </select>
                </div>
            @endforeach
            @foreach(['display', 'type'] as $field)
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">@lang("partials.$field")<span
                            class="text-danger">*</span></label>
                    <select name="{{ $field }}" class="form-control">
                        @foreach (\Modules\Attribute\Models\Attribute::${strtoupper($field).'S'} as $value)
                            <option value="{{ $value }}" @selected($attribute->$field == $value)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
    </div>
    <div class="form-group mb-3">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
