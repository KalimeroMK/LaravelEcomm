@php use Illuminate\Support\Carbon;use Illuminate\Support\Collection; @endphp
@extends('admin::layouts.master')
@section('title','Product Edit')
@section('content')
    <div class="container-fluid">
        <form method="POST"
              action="{{ isset($product['id']) ? route('products.update', $product['id']) : route('products.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($product['id']))
                @method('put')
            @endif
            <div class="row">
                <div class="col-9">
                    <div class="form-group">
                        <label for="inputTitle">@lang('partials.title')</label>
                        <input id="inputTitle" type="text" name="title" placeholder="@lang('partials.title')"
                               value="{{ old('title', $product['title'] ?? '') }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="summary">@lang('partials.summary')</label>
                        <textarea class="form-control" id="summary"
                                  name="summary">{{ old('summary', $product['summary'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">@lang('partials.description')</label>
                        <textarea class="form-control" id="description"
                                  name="description">{{ old('description', $product['description'] ?? '') }}</textarea>
                        @if(config('openai.openai.enabled'))
                            <button type="button" style="margin-top: 2%" id="generateDescription"
                                    class="btn btn-info">@lang('partials.generate_description')</button>
                        @endif
                    </div>

                    <!-- Attributes -->
                    <div class="attributes-section">
                        <h4>@lang('attributes')</h4>
                        <div id="attributes-container">
                            @php
                                $attributeValuesMap = [];
                                if (isset($product) && is_object($product) && isset($product->attributeValues)) {
                                    foreach ($product->attributeValues as $av) {
                                        $attributeValuesMap[$av->attribute->code] =
                                            $av->text_value ??
                                            $av->string_value ??
                                            $av->integer_value ??
                                            $av->float_value ??
                                            $av->decimal_value ??
                                            $av->boolean_value ??
                                            $av->date_value ??
                                            $av->url_value ??
                                            $av->hex_value ??
                                            null;
                                    }
                                }
                            @endphp
                            @foreach($attributes as $attribute)
                                @php
                                    $value = old('attributes.' . $attribute->code, $attributeValuesMap[$attribute->code] ?? '');
                                    $isCustom = ($value && (!isset($attribute->options) || !collect($attribute->options)->pluck('value')->contains($value)));
                                @endphp
                                <div class="mb-3">
                                    <label for="attribute_{{ $attribute->code }}">{{ $attribute->name }}</label>
                                    @if (!empty($attribute->options) && count($attribute->options))
                                        <select name="attributes[{{ $attribute->code }}]"
                                                id="attribute_{{ $attribute->code }}"
                                                class="form-control attribute-select"
                                                data-attribute="{{ $attribute->code }}">
                                            <option value="">-- Select --</option>
                                            @foreach ($attribute->options as $option)
                                                <option value="{{ $option->value }}" {{ $value == $option->value ? 'selected' : '' }}>{{ $option->label ?? $option->value }}</option>
                                            @endforeach
                                            <option value="custom" {{ $isCustom ? 'selected' : '' }}>Custom</option>
                                        </select>
                                        <input type="text"
                                               name="attributes_custom[{{ $attribute->code }}]"
                                               class="form-control custom-attribute-input mt-2"
                                               id="custom-input-{{ $attribute->code }}"
                                               value="{{ $isCustom ? $value : '' }}"
                                               style="display: {{ $isCustom ? 'block' : 'none' }};"
                                               placeholder="Enter custom value">
                                    @else
                                        <input type="text" class="form-control"
                                               name="attributes[{{ $attribute->code }}]"
                                               value="{{ $value }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="images">@lang('partials.image')</label>
                        <input type="file" name="images[]" id="image" class="form-control">
                        @if(isset($product) && is_object($product) && $product->hasMedia('images'))
                            <div class="mt-2">
                                <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}" alt="Product Image"
                                     style="max-width: 150px; max-height: 150px;">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="sku">@lang('partials.sku')</label>
                        <input id="sku" name="sku" placeholder="Enter SKU"
                               value="{{ old('sku', $product['sku'] ?? '') }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="price">@lang('partials.price')</label>
                        <input id="price" type="number" name="price" placeholder="Enter price"
                               value="{{ old('price', $product['price'] ?? '') }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="special_price">@lang('partials.special_price')</label>
                        <input id="special_price" type="number" name="special_price"
                               value="{{ old('special_price', $product['special_price'] ?? '') }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="special_price_end">@lang('partials.start_date')</label>
                        <input type="date" name="special_price_start" id="special_price_start"
                               value="{{ old('special_price_start') ?? (isset($product['special_price_start']) ? Carbon::parse($product['special_price_start'])->format('Y-m-d') : '') }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="special_price_end">@lang('partials.end_date')</label>
                        <input type="date" name="special_price_end" id="special_price_end"
                               value="{{ old('special_price_end') ?? (isset($product['special_price_end']) ? Carbon::parse($product['special_price_end'])->format('Y-m-d') : '') }}"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ old('status', $product['status'] ?? '') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('status', $product['status'] ?? '') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="is_featured">@lang('partials.is_featured')</label><br>
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                {{ old('is_featured', $product['is_featured'] ?? false) ? 'checked' : '' }}> Yes
                    </div>

                    <div class="form-group">
                        <label for="brand_id">@lang('sidebar.brands')</label>
                        <select name="brand_id" class="form-control">
                            <option value="">@lang('partials.select')</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand['id'] }}" {{ old('brand_id', $product['brand']['id'] ?? '') == $brand['id'] ? 'selected' : '' }}>{{ $brand['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">@lang('partials.categories')</label>
                        <select name="category[]" id="category_id" class="form-control js-example-basic-multiple"
                                multiple="multiple">
                            @php
                                $selectedCategories = old('category', isset($product['categories']) ? collect($product['categories'])->pluck('id')->all() : []);
                            @endphp
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}" {{ in_array($category['id'], $selectedCategories) ? 'selected' : '' }}>{{ $category['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tag">@lang('partials.tags')</label>
                        <select name="tag[]" class="form-control js-example-basic-multiple" multiple="multiple">
                            @php
                                $selectedTags = old('tag', isset($product['tags']) ? collect($product['tags'])->pluck('id')->all() : []);
                            @endphp
                            @foreach($tags as $tag)
                                <option value="{{ $tag['id'] }}" {{ in_array($tag['id'], $selectedTags) ? 'selected' : '' }}>{{ $tag['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stock">Quantity</label>
                        <input id="stock" type="number" name="stock" min="0" placeholder="Enter quantity"
                               value="{{ old('stock', $product['stock'] ?? '') }}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="button-container">
                <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
            </div>
        </form>
    </div>

    @if(isset($product) && is_object($product))
    <div class="row container-fluid mt-4">
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
    @endif

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

            function setupAttributeSelects() {
                document.querySelectorAll('.attribute-select').forEach(function (select) {
                    select.addEventListener('change', function () {
                        var attrCode = this.getAttribute('data-attribute');
                        var customInput = document.getElementById('custom-input-' + attrCode);
                        if (this.value === 'custom') {
                            customInput.style.display = 'block';
                        } else {
                            customInput.style.display = 'none';
                            customInput.value = '';
                        }
                    });
                });
            }

            setupAttributeSelects();
        </script>
    @endpush
@endsection
