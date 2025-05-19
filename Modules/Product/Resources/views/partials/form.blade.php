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
                                  name="summary">{{ $product->summary ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">@lang('partials.description')</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ $product->description ?? '' }}</textarea>
                        @if(config('openai.openai.enabled'))
                            <button type="button" style="margin-top: 2%" id="generateDescription"
                                    class="btn btn-info">@lang('partials.generate_description')</button>
                        @endif
                    </div>

                    <!-- Attributes -->
                    <div class="attributes-section">
                        <h4>@lang('partials.product_atributes')</h4>
                        @foreach ($attributes as $attribute)
                            <div class="mb-3"
                                 x-data="{ selected: '{{ old('attributes.' . $attribute->code, $selectedValues[0] ?? '') }}' }">
                                <label for="attribute_{{ $attribute->code }}">{{ $attribute->name }}</label>
                                @php
                                    $selectedValues = $product?->attributeValues
                                        ->where('attribute_id', $attribute->id)
                                        ->pluck($attribute->getValueColumnName())
                                        ->filter(fn($v) => $v !== null)
                                        ->values()
                                        ->toArray();
                                @endphp

                                @if ($attribute->display === 'checkbox' && $attribute->options->count())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($attribute->options as $option)
                                            <div class="form-check form-check-inline">
                                                <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="attributes[{{ $attribute->code }}][]"
                                                        id="attribute_{{ $attribute->code }}_{{ $option->id }}"
                                                        value="{{ $option->value }}"
                                                        {{ in_array($option->value, old('attributes.' . $attribute->code, $selectedValues)) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label"
                                                       for="attribute_{{ $attribute->code }}_{{ $option->id }}">
                                                    {{ $option->value }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif ($attribute->display === 'checkbox' && $attribute->options->isEmpty())
                                    <div class="form-check">
                                        <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="attributes[{{ $attribute->code }}]"
                                                id="attribute_{{ $attribute->code }}"
                                                value="1"
                                                {{ old('attributes.' . $attribute->code, $selectedValues[0] ?? false) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="attribute_{{ $attribute->code }}">
                                            {{ $attribute->name }}
                                        </label>
                                    </div>
                                @elseif ($attribute->options->count())
                                    <select
                                            x-model="selected"
                                            name="attributes[{{ $attribute->code }}]"
                                            id="attribute_{{ $attribute->code }}"
                                            class="form-control"
                                    >
                                        <option value="">-- Select {{ $attribute->name }} --</option>
                                        @foreach ($attribute->options as $option)
                                            <option value="{{ $option->value }}"
                                                    {{ in_array($option->value, $selectedValues) ? 'selected' : '' }}>
                                                {{ $option->value }}
                                            </option>
                                        @endforeach
                                        <option value="__custom__">Other (specify manually)</option>
                                    </select>

                                    <template x-if="selected === '__custom__'">
                                        <input
                                                type="text"
                                                name="attributes_custom[{{ $attribute->code }}]"
                                                placeholder="Enter custom {{ $attribute->name }}"
                                                class="form-control mt-2"
                                        />
                                    </template>
                                @else
                                    <input
                                            type="text"
                                            name="attributes[{{ $attribute->code }}]"
                                            id="attribute_{{ $attribute->code }}"
                                            class="form-control"
                                            value="{{ old('attributes.' . $attribute->code, $selectedValues[0] ?? '') }}"
                                    />
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-3">
                    <!-- Other fields preserved here -->
                    <div class="form-group">
                        <label for="inputImage">@lang('partials.image')</label>
                        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
                        @if($errors->has('images'))
                            <span class="text-danger">{{ $errors->first('images') }}</span>
                        @endif
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
                        <input type="hidden" name="category" value="">
                        <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                                multiple="multiple" required>
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

                    </div>
                    <div class="form-group">
                        <label for="stock">Quantity <span class="text-danger">*</span></label>
                        <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"
                               value="{{ $product->stock ?? ''}}" class="form-control">
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


                </div>
            </div>

            <div class="button-container">
                <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
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

    @push('styles')
        <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
    @endpush

    @push('scripts')
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#description').summernote({
                    placeholder: "Write short description.....",
                    tabsize: 2,
                    height: 150
                });

                @if(config('openai.openai.enabled'))
                $('#generateDescription').on('click', function () {
                    const productTitle = $('#inputTitle').val();
                    if (!productTitle) {
                        alert('Please enter a title first.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('products.generate-description') }}",
                        type: "POST",
                        data: {
                            title: productTitle,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#description').summernote('code', response.description);
                        },
                        error: function () {
                            alert('Error generating description. Please try again.');
                        }
                    });
                });
                @endif
            });
        </script>
    @endpush
@endsection