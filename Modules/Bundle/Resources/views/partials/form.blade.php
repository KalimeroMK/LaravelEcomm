@if ($bundle->exists)
    <form class="form-horizontal" method="POST" action="{{ route('bundles.update', $bundle->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('bundles.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif

                {{-- Bundle Fields --}}
                <div class="form-group row">
                    <div class="col-9">
                        {{-- Name Input --}}
                        <label for="inputTitle" class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="name" placeholder="Enter name"
                               value="{{ $bundle->name ?? '' }}" class="form-control">

                        {{-- Description Textarea --}}
                        <label for="description" class="col-form-label">Description</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ $bundle->description ?? '' }}</textarea>

                        {{-- Price Input --}}
                        <label for="inputPrice" class="col-form-label">Price <span class="text-danger">*</span></label>
                        <input id="inputPrice" type="text" name="price" placeholder="Enter price"
                               value="{{ $bundle->price ?? '' }}" class="form-control">
                    </div>

                    <div class="col-3">
                        {{-- Products Select --}}
                        <div class="form-group">
                            <label for="products">Products</label>
                            <select class="form-control js-example-basic-multiple" id="products" name="products[]"
                                    multiple="multiple">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Image Upload --}}
                        <div class="form-group">
                            <label for="inputImage" class="col-form-label">Image <span
                                        class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>

            {{-- Images Display and Deletion --}}
            <div class="row">
                @foreach($bundle->getMedia('bundle') as $media)
                    <div class="col-md-3">
                        <div class="image">
                            <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
                            <form action="{{ route('bundle.delete-media', ['modelId' => $bundle->id, 'mediaId' => $media->id]) }}"
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
                {{-- CKEditor Script --}}
                <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
                <script>
                    CKEDITOR.replace('description', {
                        filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
                        filebrowserUploadMethod: 'form'
                    });
                </script>
    @endpush
