{{-- Configurable Product Section --}}
<div class="card mt-4" id="configurable-section" style="display: {{ ($product['type'] ?? '') === 'configurable' ? 'block' : 'none' }}">
    <div class="card-header">
        <h5 class="mb-0">@lang('partials.product_type_configurable') @lang('partials.settings')</h5>
    </div>
    <div class="card-body">
        {{-- Product Type Selection --}}
        <div class="form-group">
            <label for="product_type">@lang('partials.product_type')</label>
            <select name="type" id="product_type" class="form-control" onchange="toggleConfigurableSection()">
                <option value="simple" {{ ($product['type'] ?? 'simple') === 'simple' ? 'selected' : '' }}>@lang('partials.product_type_simple')</option>
                <option value="configurable" {{ ($product['type'] ?? '') === 'configurable' ? 'selected' : '' }}>@lang('partials.product_type_configurable')</option>
                <option value="downloadable" {{ ($product['type'] ?? '') === 'downloadable' ? 'selected' : '' }}>@lang('partials.product_type_downloadable')</option>
                <option value="virtual" {{ ($product['type'] ?? '') === 'virtual' ? 'selected' : '' }}>@lang('partials.product_type_virtual')</option>
            </select>
        </div>

        {{-- Configurable Attributes Selection --}}
        <div id="configurable-attributes-section" style="display: {{ ($product['type'] ?? '') === 'configurable' ? 'block' : 'none' }}">
            <div class="form-group">
                <label>@lang('partials.select_configurable_attributes')</label>
                <div class="alert alert-info">
                    @lang('partials.configurable_attributes_info')
                </div>
                <div class="row">
                    @foreach($attributes as $attribute)
                        @if($attribute->is_configurable || $attribute->options->count() > 0)
                            <div class="col-md-4 mb-2">
                                <label class="d-block p-2 border rounded">
                                    <input type="checkbox" name="configurable_attributes[]" 
                                           value="{{ $attribute->code }}"
                                           {{ in_array($attribute->code, $product['configurable_attributes'] ?? []) ? 'checked' : '' }}>
                                    {{ $attribute->name }}
                                    <small class="text-muted d-block">{{ $attribute->options->count() }} @lang('partials.options')</small>
                                </label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Generate Variants Button --}}
            <div class="form-group">
                <label class="d-flex align-items-center">
                    <input type="checkbox" name="generate_variants" value="1" class="mr-2">
                    @lang('partials.auto_generate_variants')
                </label>
                <label class="d-flex align-items-center mt-2">
                    <input type="checkbox" name="regenerate_variants" value="1" class="mr-2">
                    @lang('partials.regenerate_variants')
                </label>
            </div>

            {{-- Variants Table --}}
            @if(isset($variants) && $variants->count() > 0)
                <div class="mt-4">
                    <h6>@lang('partials.existing_variants') ({{ $variants->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('partials.variant')</th>
                                    <th>@lang('partials.sku')</th>
                                    <th>@lang('partials.price')</th>
                                    <th>@lang('partials.stock')</th>
                                    <th>@lang('partials.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($variants as $variant)
                                    <tr>
                                        <td>{{ $variant->variant_name }}</td>
                                        <td>{{ $variant->sku }}</td>
                                        <td>
                                            <input type="number" name="variant_prices[{{ $variant->id }}]" 
                                                   value="{{ $variant->price }}" class="form-control form-control-sm" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" name="variant_stocks[{{ $variant->id }}]" 
                                                   value="{{ $variant->stock }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $variant->id) }}" class="btn btn-sm btn-info">@lang('partials.edit')</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleConfigurableSection() {
        const type = document.getElementById('product_type').value;
        const section = document.getElementById('configurable-section');
        const attrsSection = document.getElementById('configurable-attributes-section');
        
        if (type === 'configurable') {
            section.style.display = 'block';
            attrsSection.style.display = 'block';
        } else {
            section.style.display = 'none';
            attrsSection.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleConfigurableSection();
    });
</script>
@endpush
