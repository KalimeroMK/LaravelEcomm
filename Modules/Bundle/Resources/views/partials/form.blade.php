@if ($bundle->exists)
    <form class="form-horizontal" method="POST" action="{{ route('bundles.update', $attribute->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('bundles.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group row">
                    <div class="col-9">
                        <label for="inputTitle" class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="name" placeholder="Enter name"
                               value="{{ $bundle->name ?? null }}"
                               class="form-control">
                        <label for="description" class="col-form-label">Description</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ $bundle->description ??''}}</textarea>
                        <label for="inputTitle" class="col-form-label">Price <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="price" placeholder="Enter code"
                               value="{{ $bundle->price ?? null }}"
                               class="form-control">
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="products">Products</label>
                            <select class="form-control js-example-basic-multiple" id="products" name="product[]"
                                    multiple="multiple">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
    </form>
    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
        <script type="text/javascript">
            CKEDITOR.replace('description', {
                filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
                filebrowserUploadMethod: 'form'
            });
        </script>
    @endpush
