<form method="POST"
      action="{{ route($post->exists ? 'posts.update' : 'posts.store', $post->exists ? $post->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($post->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputTitle">@lang('partials.title')</label>
        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')" value="{{$post->title}}"
               class="form-control">
    </div>

    <div class="form-group">
        <label for="quote">@lang('partials.quote')</label>
        <textarea class="form-control" id="quote" name="quote">{{$post->quote}}</textarea>
    </div>

    <div class="form-group">
        <label for="summary">@lang('partials.summary')</label>
        <textarea class="form-control" id="summary" name="summary">{{$post->summary}}</textarea>
    </div>

    <div class="form-group">
        <label for="description">@lang('partials.description')</label>
        <textarea class="form-control" id="description" name="description">{{$post->description}}</textarea>
    </div>

    <div class="form-group">
        <label for="inputImage">@lang('partials.image')</label>
        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" class="form-control">
            <option value="active" {{ $post->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $post->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
</form>
