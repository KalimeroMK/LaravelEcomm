@extends('admin::layouts.master')
@section('title','Product Edit')
@section('content')
    <div class="container-fluid">
        <form method="POST"
              action="{{ route($product->exists ? 'products.update' : 'products.store', $product->exists ? $product->id : null) }}"
              enctype="multipart/form-data">
            @csrf
            @if($product->exists)
                @method('put')
            @endif
            <div class="row">
                <div class="col-9">
                    <div class="form-group">
                        <label for="inputTitle">@lang('partials.title')</label>
                        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
                               value="{{ $product->title ?? '' }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="summary">@lang('partials.summary')</label>
                        <textarea class="form-control" id="summary"
                                  name="summary">{{$product->summary ?? ''}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">@lang('partials.description')</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ $product->description ??''}}</textarea>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="inputImage">@lang('partials.image')</label>
                        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('partials.sku')</label>
                        <input id="price" type="number" name="sku" placeholder="Enter SKU"
                               value="{{ $product->sku ?? '' }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="price">@lang('partials.price')</label>
                        <input id="price" type="number" name="price" placeholder="Enter price"
                               value="{{ $product->price ?? '' }}"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="stock">@lang('partials.special_price') <span class="text-danger">*</span></label>
                        <input id="quantity" type="number" name="special_price" min="0"
                               placeholder="@lang('partials.special_price')"
                               value="{{ $product->special_price ?? ''}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="stock">@lang('partials.start_date') <span class="text-danger">*</span></label>
                        <input id="quantity" type="date" name="special_price_start"
                               placeholder="@lang('partials.start_date')"
                               value="{{ $product->special_price_start ?? ''}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="stock">@lang('partials.end_date') <span class="text-danger">*</span></label>
                        <input id="quantity" type="date" name="special_price_end"
                               placeholder="@lang('partials.end_date')"
                               value="{{ $product->special_price_end ?? ''}}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option
                                value="active" {{ (isset($product->status) && $product->status == 'active') ? 'selected' : '' }}>
                                Active
                            </option>
                            <option
                                value="inactive" {{ (isset($product->status) && $product->status == 'inactive') ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="is_featured">@lang('partials.is_featured')</label><br>
                        <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
                    </div>

                    <div class="form-group">
                        <label for="cat_id">@lang('partials.categories')</label>
                        <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                                multiple="multiple">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cat_id">@lang('partials.tag')</label>
                        <select class="form-control js-example-basic-multiple" id="tag" name="tag[]"
                                multiple="multiple">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">@lang('partials.conditions')</label>
                        <select name="condition_id" class="form-control" required>
                            @foreach($conditions as $condition)
                                <option
                                    value="{{ $condition->id }}" {{ (isset($product->condition_id) && $product->condition_id == $condition->id) ? 'selected' : '' }}>
                                    {{ $condition->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock">Quantity <span class="text-danger">*</span></label>
                        <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"
                               value="{{ $product->stock ?? ''}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="color" class="col-form-label">Color</label>
                        <input id="color" type="text" name="color[]" placeholder="Enter color"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="brand_id">Brand</label>
                        <select name="brand_id" class="form-control">
                            <option value="">--Select Brand--</option>
                            @foreach($brands as $brand)
                                <option value="{{$brand->id}}" @if (!empty($product->brand->id))
                                    {{($brand->id==$product->brand->id)? 'selected':'' }}
                                    @endif>{{$brand->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select class="form-control js-example-basic-multiple" id="size" name="size[]"
                                multiple="multiple">
                            @foreach ($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="button-container">
                    <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                    <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
                </div>
            </div>

        </form>
    </div>
    <div class="row container-fluid" style="margin-top: 2%">
        @foreach($product->getMedia('product') as $media)
            <div class="col-md-3">
                <img src="{{ $media->getUrl() }}" alt="Image" class="img-fluid" style="max-height: 300px">
                <form action="{{ route('product.delete-media', ['modelId' => $product->id, 'mediaId' => $media->id]) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('description', {
                versionCheck: false,
                filebrowserUploadUrl: "{{ route('ckeditor.image-upload', ['_token' => csrf_token() ]) }}",
                filebrowserUploadMethod: 'form'
            });
        </script>
    @endpush
@endsection
