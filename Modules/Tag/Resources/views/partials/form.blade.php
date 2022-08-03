@if ($tag->exists)

    <form class="form-horizontal" method="POST" action="{{route('tags.update',$tag->id)}}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('tags.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title</label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                           value="{{$tag->title}}" class="form-control">

                </div>
                <div class="form-group">
                    <label for="status" class="col-form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($tag->status=='active') ? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($tag->status=='inactive') ? 'selected' : '')}}>Inactive
                        </option>
                    </select>

                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
    </form>
