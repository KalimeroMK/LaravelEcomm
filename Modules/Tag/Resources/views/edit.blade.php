@extends('tag::layouts.master')

@section('content')

    <div class="card">
        <h5 class="card-header">Edit Post Tag</h5>
        <div class="card-body">
            <form method="post" action="{{route('tags.update',$tag->id}}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title</label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                           value="{{$postTag->title}}" class="form-control">

                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($postTag->status=='active') ? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($postTag->status=='inactive') ? 'selected' : '')}}>Inactive
                        </option>
                    </select>
                    @error('status')

                    @enderror
                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection
