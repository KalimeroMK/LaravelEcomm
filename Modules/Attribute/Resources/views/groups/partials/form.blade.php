<form class="form-horizontal" method="POST"
      action="{{ route($group->exists ? 'attribute-groups.update' : 'attribute-groups.store', $group->exists ? $group->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($group->exists)
        @method('put')
    @endif
    <div class="form-group row">
        <div class="col-6">
            <label for="inputTitle" class="col-form-label">@lang('partials.name') <span class="text-danger">*</span></label>
            <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
                   value="{{ $group->name ?? null }}"
                   class="form-control">
        </div>
        <div class="col-6">
            <label for="inputDescription" class="col-form-label">@lang('partials.description')</label>
            <input id="inputDescription" type="text" name="description" placeholder="@lang('partials.description')"
                   value="{{ $group->description ?? null }}"
                   class="form-control">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <label for="attributes" class="col-form-label">@lang('partials.attributes')</label>
            <select name="attributes[]" id="attributes" class="form-control" multiple>
                @foreach (\Modules\Attribute\Models\Attribute::all() as $attribute)
                    <option value="{{ $attribute->id }}"
                        @if(isset($group) && $group->attributes->contains($attribute->id)) selected @endif>
                        {{ $attribute->name }} ({{ $attribute->code }})
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
        </div>
    </div>
    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
