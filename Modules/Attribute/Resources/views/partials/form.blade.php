@if ($attribute->exists)
    <form class="form-horizontal" method="POST" action="{{ route('attribute.update', $attribute->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('attribute.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group row">
                    <div class="col-6">
                        <label for="inputTitle" class="col-form-label">@lang('partials.name') <span
                                    class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
                               value="{{ $attribute->name ?? null }}"
                               class="form-control">
                        <label for="inputTitle" class="col-form-label">@lang('partials.code') <span
                                    class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="code" placeholder="@lang('partials.code')"
                               value="{{ $attribute->code ?? null }}"
                               class="form-control">

                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">@lang('partials.filterable') <span
                                        class="text-danger">*</span></label>
                            <select name="filterable" class="form-control">
                                <option @selected(old('filterable',$attribute->filterable === 1))
                                        value="1">@lang('partials.yes')
                                </option>
                                <option @selected(old('filterable',$attribute->filterable === 0))
                                        value="0">@lang('partials.no')
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-form-label">@lang('partials.configurable')<span
                                        class="text-danger">*</span></label>
                            <select name="configurable" class="form-control">
                                <option @selected(old('configurable',$attribute->configurable === 1))
                                        value="1">@lang('partials.yes')
                                </option>
                                <option @selected(old('configurable',$attribute->configurable === 0))
                                        value="0">@lang('partials.no')
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputTitle" class="col-form-label">@lang('partials.display')<span
                                        class="text-danger">*</span></label>
                            <select name="display" class="form-control">
                                @foreach (\Modules\Attribute\Models\Attribute::DISPLAYS as $value)
                                    <option value="{{ $value }}" @selected($attribute->display == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputTitle" class="col-form-label">@lang('partials.type') <span
                                        class="text-danger">*</span></label>
                            <select name="type" class="form-control">
                                @foreach (\Modules\Attribute\Models\Attribute::TYPES as $value)
                                    <option value="{{ $value }}" @selected($attribute->type == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                    <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
                </div>
            </form>
    </form>
