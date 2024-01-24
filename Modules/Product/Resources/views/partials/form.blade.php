@if ($product->exists)
    <form class="form-horizontal" method="POST" action="{{ route('product.update', $product->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('product.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="row">
                    <div class="form-group col-9">
                        <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                        <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                               value="{{ $product->title ?? '' }}"
                               class="form-control">
                        <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="summary"
                                  name="summary">{{$product->summary ?? ''}}</textarea>
                        <label for="description" class="col-form-label">Description</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ $product->description ??''}}</textarea>
                    </div>
                    <div class="form-group col-3">
                        <div class="form-group">
                            <label for="inputImage" class="col-form-label">Image <span
                                        class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
                        </div>
                        <label for="price" class="col-form-label">Price <span class="text-danger">*</span></label>
                        <input id="price" type="number" name="price" placeholder="Enter price"
                               value="{{ $product->price ?? '' }}" class="form-control">
                        <label for="price" class="col-form-label">Special price<span
                                    class="text-danger">*</span></label>
                        <input id="price" type="number" name="special_price" placeholder="Enter special price"
                               value="{{ $product->special_price ?? '' }}" class="form-control">
                        <label for="price" class="col-form-label">Special price start date<span
                                    class="text-danger">*</span></label>
                        <input id="price" type="date" name="special_price_start"
                               value="{{ $product->special_price_start ?? '' }}" class="form-control">
                        <label for="price" class="col-form-label">Special price start date<span
                                    class="text-danger">*</span></label>
                        <input id="price" type="date" name="special_price_start"
                               value="{{ $product->special_price_start ?? '' }}" class="form-control">
                        <label for="price" class="col-form-label">Stock count<span
                                    class="text-danger">*</span></label>
                        <input id="price" type="number" name="stock" placeholder="Enter stock value"
                               value="{{ $product->stock ?? '' }}" class="form-control">
                        <label for="cat_id">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">--Select status--</option>
                            <option value="active" {{ (isset($product->status) && $product->status == 'active') ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ (isset($product->status) && $product->status == 'inactive') ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="is_featured">Is Featured</label><br>
                    <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
                </div>
                <div class="form-group row">
                    <div class="col-3">
                        <label for="cat_id">Category <span class="text-danger">*</span></label>
                        <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                                multiple="multiple">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group col-3">
                        <label for="size">Size</label>
                        <select class="form-control js-example-basic-multiple" id="size" name="size[]"
                                multiple="multiple">
                            @foreach ($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="color" class="col-form-label">Color</label>
                        <input id="color" type="text" name="color[]" placeholder="Enter color"
                               class="form-control">
                    </div>
                    <div class="form-group col-3">
                        <label for="size">Tags</label>
                        <select class="form-control js-example-basic-multiple" id="tags" name="tag[]"
                                multiple="multiple">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="condition_id">Condition</label>
                        <select name="condition_id" class="form-control">
                            <option value="">--Select condition--</option>
                            @foreach($conditions as $condition)
                                <option value="{{$condition->id}}"@if (!empty($product->condition->id))
                                    {{($condition->id==$product->condition->id)? 'selected':'' }}
                                        @endif>{{$condition->status}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @foreach ($attributes as $attribute)
                        @php
                            $attributeValue = $product->attributeValues->where('attribute_id', $attribute->id)->first();
                        @endphp

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="attribute_{{ $attribute->id }}">{{ $attribute->name }}</label>

                                @switch($attribute->type)
                                    @case('url')
                                    @case('text')
                                    @case('string')
                                        <input type="text" class="form-control" name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('hex')
                                        <input type="color" class="form-control" name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('date')
                                        <input type="date" class="form-control" name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('time')
                                        <input type="time" class="form-control" name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('float')
                                    @case('decimal')
                                        <input type="number" step="0.01" class="form-control"
                                               name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('integer')
                                        <input type="number" class="form-control"
                                               name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                        @break

                                    @case('boolean')
                                        <select class="form-control" name="attributes[{{ $attribute->id }}]"
                                                id="attribute_{{ $attribute->id }}">
                                            <option value="1" {{ $attributeValue?->value == '1' ? 'selected' : '' }}>
                                                True
                                            </option>
                                            <option value="0" {{ $attributeValue?->value == '0' ? 'selected' : '' }}>
                                                False
                                            </option>
                                        </select>
                                        @break

                                    @default
                                        <input type="text" class="form-control" name="attributes[{{ $attribute->id }}]"
                                               id="attribute_{{ $attribute->id }}"
                                               value="{{ $attributeValue->value ?? '' }}">
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Submit</button>
            </form>
            <div class="row">
                @foreach($product->getMedia('post') as $media)
                    <div class="col-md-3">
                        <div class="image">
                            <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid">
                            <form action="{{ route('products.delete-media', ['modelId' => $product->id, 'mediaId' =>
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
                        filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
                        filebrowserUploadMethod: 'form'
                    });
                </script>
    @endpush
