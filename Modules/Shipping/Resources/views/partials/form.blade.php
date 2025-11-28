<form method="post" action="{{ isset($shipping['id']) ? route('admin.shipping.update', $shipping['id']) : route('admin.shipping.store') }}">
    @csrf
    @if(isset($shipping['id']))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.type') <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="type" placeholder="@lang('partials.title')" value="{{ $shipping['type'] ?? old('type') }}" class="form-control">
        @error('type')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="form-group">
        <label for="price" class="col-form-label">@lang('partials.price') <span class="text-danger">*</span></label>
        <input id="price" type="number" name="price" placeholder="Enter price" value="{{ $shipping['price'] ?? old('price') }}" class="form-control">
        @error('price')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="form-group">
        <label for="status" class="col-form-label">@lang('partials.status') <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="active" {{ (isset($shipping['status']) && $shipping['status'] == 'active') ? 'selected' : '' }}>@lang('partials.active')</option>
            <option value="inactive" {{ (isset($shipping['status']) && $shipping['status'] == 'inactive') ? 'selected' : '' }}>@lang('partials.inactive')</option>
        </select>
        @error('status')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>
