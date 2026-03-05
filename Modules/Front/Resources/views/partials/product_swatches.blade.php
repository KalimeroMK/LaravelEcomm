{{-- Product Attribute Swatches --}}
@if(isset($product) && $product->attributeValues->count() > 0)
    @php
        $groupedAttributes = $product->attributeValues->groupBy(function($av) {
            return $av->attribute->name;
        });
    @endphp

    <div class="product-attributes mt-4">
        @foreach($groupedAttributes as $attributeName => $attributeValues)
            @php
                $attribute = $attributeValues->first()->attribute;
                $display = $attribute->display;
            @endphp
            
            <div class="attribute-group mb-3">
                <h6 class="attribute-name">{{ $attributeName }}</h6>
                
                @switch($display)
                    @case('color')
                        {{-- Color Swatches --}}
                        <div class="color-swatches d-flex flex-wrap gap-2">
                            @foreach($attributeValues as $av)
                                @php
                                    $colorValue = $av->getValue();
                                    $colorHex = $av->attribute->options->firstWhere('value', $colorValue)?->color_hex ?? $colorValue;
                                @endphp
                                <label class="color-swatch-label">
                                    <input type="radio" 
                                           name="attribute_{{ $attribute->code }}" 
                                           value="{{ $colorValue }}"
                                           class="d-none"
                                           @if($loop->first) checked @endif
                                           onchange="updateVariantSelection('{{ $attribute->code }}', '{{ $colorValue }}')">
                                    <span class="color-swatch" 
                                          style="background-color: {{ $colorHex }};"
                                          title="{{ $colorValue }}">
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('button')
                        {{-- Button Swatches --}}
                        <div class="button-swatches d-flex flex-wrap gap-2">
                            @foreach($attributeValues as $av)
                                @php
                                    $value = $av->getValue();
                                    $label = $av->attribute->options->firstWhere('value', $value)?->label ?? $value;
                                @endphp
                                <label class="btn-swatch-label">
                                    <input type="radio" 
                                           name="attribute_{{ $attribute->code }}" 
                                           value="{{ $value }}"
                                           class="d-none"
                                           @if($loop->first) checked @endif
                                           onchange="updateVariantSelection('{{ $attribute->code }}', '{{ $value }}')">
                                    <span class="btn btn-outline-secondary btn-swatch">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('image')
                        {{-- Image Swatches --}}
                        <div class="image-swatches d-flex flex-wrap gap-2">
                            @foreach($attributeValues as $av)
                                @php
                                    $value = $av->getValue();
                                    $image = $av->attribute->options->firstWhere('value', $value)?->image;
                                    $label = $av->attribute->options->firstWhere('value', $value)?->label ?? $value;
                                @endphp
                                <label class="image-swatch-label">
                                    <input type="radio" 
                                           name="attribute_{{ $attribute->code }}" 
                                           value="{{ $value }}"
                                           class="d-none"
                                           @if($loop->first) checked @endif
                                           onchange="updateVariantSelection('{{ $attribute->code }}', '{{ $value }}')">
                                    <span class="image-swatch" title="{{ $label }}">
                                        @if($image)
                                            <img src="{{ asset($image) }}" alt="{{ $label }}" class="img-thumbnail">
                                        @else
                                            <span class="btn btn-outline-secondary">{{ $label }}</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    @default
                        {{-- Default Dropdown --}}
                        <select name="attribute_{{ $attribute->code }}" 
                                class="form-control"
                                onchange="updateVariantSelection('{{ $attribute->code }}', this.value)">
                            @foreach($attributeValues as $av)
                                @php
                                    $value = $av->getValue();
                                    $label = $av->attribute->options->firstWhere('value', $value)?->label ?? $value;
                                @endphp
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                @endswitch
            </div>
        @endforeach
    </div>
@endif

@push('styles')
<style>
    .product-attributes .attribute-name {
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #666;
    }

    /* Color Swatches */
    .color-swatch-label {
        cursor: pointer;
        margin: 0;
    }

    .color-swatch {
        display: block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #ddd;
        transition: all 0.2s;
        position: relative;
    }

    .color-swatch-label input:checked + .color-swatch {
        border-color: #333;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #333;
    }

    .color-swatch:hover {
        transform: scale(1.1);
    }

    /* Button Swatches */
    .btn-swatch-label {
        cursor: pointer;
        margin: 0;
    }

    .btn-swatch {
        min-width: 40px;
        padding: 0.375rem 0.75rem;
        transition: all 0.2s;
    }

    .btn-swatch-label input:checked + .btn-swatch {
        background-color: #333;
        color: white;
        border-color: #333;
    }

    /* Image Swatches */
    .image-swatch-label {
        cursor: pointer;
        margin: 0;
    }

    .image-swatch img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        transition: all 0.2s;
    }

    .image-swatch-label input:checked + .image-swatch img {
        border-color: #333;
        box-shadow: 0 0 0 2px #333;
    }
</style>
@endpush

@push('scripts')
<script>
    // Store selected attributes
    let selectedAttributes = {};

    function updateVariantSelection(attributeCode, value) {
        selectedAttributes[attributeCode] = value;
        
        // If this is a configurable product, try to find matching variant
        @if(isset($product) && $product->isConfigurable())
            checkVariantAvailability();
        @endif
    }

    @if(isset($product) && $product->isConfigurable())
    function checkVariantAvailability() {
        // Check if all configurable attributes are selected
        const configurableAttributes = @json($product->configurable_attributes ?? []);
        const allSelected = configurableAttributes.every(attr => selectedAttributes[attr]);
        
        if (allSelected) {
            // Make AJAX request to find variant
            fetch('{{ route("admin.products.variant.by-attributes", $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ attributes: selectedAttributes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProductDisplay(data.variant);
                } else {
                    showOutOfStock();
                }
            });
        }
    }

    function updateProductDisplay(variant) {
        // Update price
        const priceElement = document.querySelector('.product-price');
        if (priceElement) {
            priceElement.textContent = '$' + parseFloat(variant.price).toFixed(2);
        }
        
        // Update SKU
        const skuElement = document.querySelector('.product-sku');
        if (skuElement) {
            skuElement.textContent = 'SKU: ' + variant.sku;
        }
        
        // Update stock status
        const stockElement = document.querySelector('.stock-status');
        if (stockElement) {
            if (variant.stock > 0) {
                stockElement.textContent = 'In Stock (' + variant.stock + ')';
                stockElement.className = 'stock-status text-success';
                document.querySelector('.add-to-cart-btn')?.removeAttribute('disabled');
            } else {
                stockElement.textContent = 'Out of Stock';
                stockElement.className = 'stock-status text-danger';
                document.querySelector('.add-to-cart-btn')?.setAttribute('disabled', 'disabled');
            }
        }
        
        // Update variant ID for add to cart
        const variantInput = document.querySelector('input[name="variant_id"]');
        if (variantInput) {
            variantInput.value = variant.id;
        }
    }

    function showOutOfStock() {
        const stockElement = document.querySelector('.stock-status');
        if (stockElement) {
            stockElement.textContent = 'Combination not available';
            stockElement.className = 'stock-status text-warning';
        }
        document.querySelector('.add-to-cart-btn')?.setAttribute('disabled', 'disabled');
    }
    @endif
</script>
@endpush
