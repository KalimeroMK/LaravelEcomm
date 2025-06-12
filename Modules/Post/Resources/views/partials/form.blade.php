<form method="POST"
      action="{{ route(($post['id'] ?? false) ? 'posts.update' : 'posts.store', $post['id'] ?? null) }}"
      enctype="multipart/form-data">
    @csrf
    @if(!empty($post['id']))
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle">@lang('partials.title')</label>
        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
               value="{{ $post['title'] ?? '' }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="cat_id">@lang('partials.categories')</label>
        <select class="form-control js-example-basic-multiple" id="category" name="category[]" multiple="multiple">
            @foreach ($categories as $category)
                <option value="{{ $category['id'] ?? $category->id }}"
                        {{ !empty($post['categories']) && in_array(($category['id'] ?? $category->id), (is_array($post['categories']) ? array_column($post['categories'], 'id') : $post['categories']->pluck('id')->toArray())) ? 'selected' : '' }}>
                    {{ $category['title'] ?? $category->title }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="cat_id">@lang('partials.tag')</label>
        <select class="form-control js-example-basic-multiple" id="tag" name="tag[]" multiple="multiple">
            @foreach ($tags as $tag)
                <option value="{{ $tag['id'] ?? $tag->id }}"
                        {{ !empty($post['tags']) && in_array(($tag['id'] ?? $tag->id), (is_array($post['tags']) ? array_column($post['tags'], 'id') : $post['tags']->pluck('id')->toArray())) ? 'selected' : '' }}>
                    {{ $tag['title'] ?? $tag->title }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="summary">@lang('partials.summary')</label>
        <textarea class="form-control" id="summary" name="summary">{{ $post['summary'] ?? '' }}</textarea>
    </div>
    <div class="form-group">
        <label for="description">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{ old('description',
        $post['description'] ?? '') }}</textarea>
    </div>
    <div class="form-group">
        <label for="inputImage">@lang('partials.image')</label>
        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" class="form-control">
            <option value="active" {{ ($post['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ ($post['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
</form>
@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <script>
        $('#description').summernote({
            tabsize: 4,
            height: 100
        });
    </script>
@endpush
