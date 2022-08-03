@extends('admin::layouts.master')

@section('content')

    <div class="card">
        <h5 class="card-header">Add Post</h5>
        <div class="card-body">
            <form method="post" action="{{route('posts.store')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{old('title')}}"
                           class="form-control">

                </div>

                <div class="form-group">
                    <label for="quote" class="col-form-label">Quote</label>
                    <textarea class="form-control" id="quote" name="quote">{{old('quote')}}</textarea>
                    @error('quote')

                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>

                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>

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

                <div class="form-group">
                    <label for="tags">Tag</label>
                    <select name="tags[]" multiple data-live-search="true"
                            class="form-control form-control js-example-basic-multiple">
                        <option value="">--Select any tag--</option>
                        @foreach($tags as $key=>$data)
                            <option value='{{$data->id}}'>{{$data->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="added_by">Author</label>
                    <select name="added_by" class="form-control">
                        <option value="">--Select any one--</option>
                        @foreach($users as $key=>$data)
                            <option value='{{$data->id}}' {{($key==0) ? 'selected' : ''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
                    <div class="input-group">
              <span class="btn btn-round btn-rose btn-file">
                    <span class="fileinput-new">Add Photo</span>
                    <input type="hidden" value="" name="photo"><input type="file"
                                                                      name="photo">
                        @error('photo')

                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')

                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <button type="reset" class="btn btn-warning">Reset</button>
                        <button class="btn btn-success" type="submit">Submit</button>
                    </div>
            </form>

        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"
          rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.js-example-basic-multiple').select2();
        });
    </script>
@endpush