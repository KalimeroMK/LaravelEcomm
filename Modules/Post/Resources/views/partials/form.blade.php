@if ($post->exists)
    <form class="form-horizontal" method="POST" action="{{ route('posts.update',$post->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('posts.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                           value="{{$post->title}}"
                           class="form-control">

                </div>

                <div class="form-group">
                    <label for="quote" class="col-form-label">Quote</label>
                    <textarea class="form-control" id="quote" name="quote">{{$post->quote}}</textarea>
                    @error('quote')

                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary">{{$post->summary}}</textarea>

                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea class="form-control" id="description"
                              name="description">{{$post->description}}</textarea>

                </div>

                <div class="form-group">
                    <label for="cat_id">Category <span class="text-danger">*</span></label>
                    <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                            multiple="multiple">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">@for ($i = 0; $i < $category->depth; $i++)
                                    -
                                @endfor {{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $post_tags=explode(',',$post->tags);
                    // dd($tags);
                @endphp
                <div class="form-group">
                    <label for="tags">Tag</label>
                    <select name="tags[]" multiple data-live-search="true" class="form-control selectpicker">
                        <option value="">--Select any tag--</option>
                        @foreach($tags as $key=>$data)

                            <option value="{{$data->title}}" {{(( in_array( "$data->title",$post_tags ) ) ? 'selected' : '')}}>{{$data->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="added_by">Author</label>
                    <select name="added_by" class="form-control">
                        <option value="">--Select any one--</option>
                        @foreach($users as $key=>$data)
                            <option value='{{$data->id}}' {{(($post->added_by==$data->id)? 'selected' : '')}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="inputImage" class="col-form-label">Image <span
                                class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
                </div>
                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($post->status=='active')? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($post->status=='inactive')? 'selected' : '')}}>Inactive
                        </option>
                    </select>

                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
            {{-- Images Display and Deletion --}}
            <div class="row">
                @foreach($post->getMedia('post') as $media)
                    <div class="col-md-3">
                        <div class="image">
                            <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
                            <form action="{{ route('posts.delete-media', ['modelId' => $post->id, 'mediaId' =>
                            $media->id]) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            @push('scripts')
                <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
                <script type="text/javascript">
                    CKEDITOR.replace('description', {
                        filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
                        filebrowserUploadMethod: 'form'
                    });
                </script>
    @endpush
