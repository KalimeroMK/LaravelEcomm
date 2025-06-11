<form class="form-horizontal" method="POST"
      action="{{ route($banner->exists ? 'banners.update' : 'banners.store', $banner->exists ? $banner->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($banner->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.title') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
               value="{{ $banner->title ?? null }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="description">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{$banner->description}}</textarea>
    </div>

    <div class="form-group">
        <label for="active_from">Active From</label>
        <input type="date" class="form-control" id="active_from" name="active_from" value="{{ old('active_from', optional($banner->active_from)->format('Y-m-d')) }}">
    </div>
    <div class="form-group">
        <label for="active_to">Active To</label>
        <input type="date" class="form-control" id="active_to" name="active_to" value="{{ old('active_to', optional($banner->active_to)->format('Y-m-d')) }}">
    </div>
    <div class="form-group">
        <label for="max_clicks">Max Clicks</label>
        <input type="number" class="form-control" id="max_clicks" name="max_clicks" value="{{ old('max_clicks', $banner->max_clicks) }}" min="0">
    </div>
    <div class="form-group">
        <label for="max_impressions">Max Impressions</label>
        <input type="number" class="form-control" id="max_impressions" name="max_impressions" value="{{ old('max_impressions', $banner->max_impressions) }}" min="0">
    </div>
    <div class="form-group">
        <label for="categories">Categories</label>
        <select name="categories[]" id="categories" class="form-control select2" multiple required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (isset($banner) && $banner->categories->contains($category->id)) ? 'selected' : '' }}>{{ $category->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="inputPhoto" class="col-form-label">@lang('partials.image') <span
                    class="text-danger">*</span></label>
        @if(isset($banner) && $banner->exists && method_exists($banner, 'getMedia') && $banner->getMedia('banner')->count())
            <div class="mb-2">
                @foreach($banner->getMedia('banner') as $media)
                    <img src="{{ $media->getUrl() }}" alt="Banner Image" style="max-height: 100px;">
                @endforeach
            </div>
        @endif
        <div class="input-group">
            <span class="btn btn-round btn-rose btn-file">
                <span class="fileinput-new"></span>
                <input type="hidden" value="" name="banner"><input type="file" name="banner[]">
            </span>
        </div>
    </div>

    <div class="form-group">
        <label for="status" class="col-form-label">@lang('partials.status') <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="active">@lang('partials.active')</option>
            <option value="inactive">@lang('partials.inactive')</option>
        </select>
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
            $('#categories').select2({
                placeholder: 'Select categories',
                allowClear: true
            });
        });
    </script>
@endpush
