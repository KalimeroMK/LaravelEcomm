@if ($tag->exists)

    <form class="form-horizontal" method="POST" action="{{route('tag.update',$tag->id)}}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('tag.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">@lang('partials.title')</label>
                    <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
                           value="{{$tag->title}}" class="form-control">

                </div>
                <div class="form-group">
                    <label for="status" class="col-form-label">@lang('partials.status')</label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($tag->status=='active') ? 'selected' : '')}}>@lang('partials.active')</option>
                        <option value="inactive" {{(($tag->status=='inactive') ? 'selected' : '')}}>@lang('partials.inactive')
                        </option>
                    </select>

                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">@lang('partials.update')</button>
                </div>
            </form>
    </form>
