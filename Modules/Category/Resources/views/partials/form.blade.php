<form class="form-horizontal" method="POST"
      action="{{ $category->exists ? route('categories.update',$category) : route('categories.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($category->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="title" class="control-label">{{trans('partials.title')}}</label>
        <input id="title" class="form-control" type="text" name="title" placeholder="@lang('sidebar.category')"
               value="{{ old('title', $category->title ?? null) }}"/>
    </div>

    @if ($categories && count($categories) > 2)
        <div class="form-group">
            <label for="name" class="control-label">{{trans('partials.sub_category')}}</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">{{ $category->getParentsNames() ?? __('sidebar.category') }}</option>
                @foreach ($categories as $categoryList)
                    <option value="{{ $categoryList['id'] }}">{{ $categoryList['title'] }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="form-group">
        <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="active">@lang('partials.active')</option>
            <option value="inactive">@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <button type="submit" class="btn">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</form>
