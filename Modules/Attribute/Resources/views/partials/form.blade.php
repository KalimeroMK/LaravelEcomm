@if ($attribute->exists)
    <form class="form-horizontal" method="POST" action="{{ route('attributes.update', $attribute->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('attributes.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group row">
                    <div class="col-6">
                        <label for="inputTitle" class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="name" placeholder="Enter name"
                               value="{{ $attribute->name ?? null }}"
                               class="form-control">
                        <label for="inputTitle" class="col-form-label">Code <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="code" placeholder="Enter code"
                               value="{{ $attribute->code ?? null }}"
                               class="form-control">

                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Filterable <span
                                        class="text-danger">*</span></label>
                            <select name="filterable" class="form-control">
                                <option @selected(old('filterable',$attribute->filterable === 1))
                                        value="1">Yes
                                </option>
                                <option @selected(old('filterable',$attribute->filterable === 0))
                                        value="0">No
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-form-label">Configurable <span
                                        class="text-danger">*</span></label>
                            <select name="configurable" class="form-control">
                                <option @selected(old('configurable',$attribute->configurable === 1))
                                        value="1">Yes
                                </option>
                                <option @selected(old('configurable',$attribute->configurable === 0))
                                        value="0">No
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputTitle" class="col-form-label">Display <span
                                        class="text-danger">*</span></label>
                            <select name="display" class="form-control">
                                @foreach (\Modules\Attribute\Models\Attribute::DISPLAYS as $value)
                                    <option value="{{ $value }}" @selected($attribute->display == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputTitle" class="col-form-label">Type <span
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
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
    </form>
