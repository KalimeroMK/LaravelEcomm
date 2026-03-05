{{-- Layered Navigation / Sidebar Filters --}}
<div class="layered-navigation">
    <h5 class="filter-title">Filters</h5>
    
    {{-- Price Filter --}}
    <div class="filter-section mb-4">
        <h6 class="filter-heading">Price</h6>
        <div class="price-range-slider">
            <input type="range" 
                   id="price-slider" 
                   class="form-control-range"
                   min="0" 
                   max="1000" 
                   value="1000"
                   data-attribute-code="price">
            <div class="d-flex justify-content-between mt-2">
                <span>$0</span>
                <span id="price-value">$1000</span>
            </div>
        </div>
    </div>

    @if(isset($filters) && count($filters) > 0)
        @foreach($filters as $filter)
            <div class="filter-section mb-4" data-attribute-code="{{ $filter['code'] }}">
                <h6 class="filter-heading">{{ $filter['name'] }}</h6>
                
                @switch($filter['type'])
                    {{-- Color Swatches Filter --}}
                    @case('swatch')
                        <div class="filter-options color-swatch-filters">
                            @foreach($filter['options'] as $option)
                                <label class="color-swatch-filter" title="{{ $option['label'] }}">
                                    <input type="checkbox" 
                                           name="{{ $filter['code'] }}" 
                                           value="{{ $option['value'] }}"
                                           class="d-none"
                                           data-attribute-code="{{ $filter['code'] }}">
                                    <span class="color-swatch" 
                                          style="background-color: {{ $option['color'] ?? $option['value'] }};"></span>
                                    @if(isset($option['count']))
                                        <small class="count">({{ $option['count'] }})</small>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @break

                    {{-- Button/Toggle Filters --}}
                    @case('button')
                        <div class="filter-options button-filters">
                            @foreach($filter['options'] as $option)
                                <label class="filter-option">
                                    <input type="checkbox" 
                                           name="{{ $filter['code'] }}" 
                                           value="{{ $option['value'] }}"
                                           class="d-none"
                                           data-attribute-code="{{ $filter['code'] }}">
                                    <span class="btn btn-outline-secondary btn-sm">
                                        {{ $option['label'] }}
                                        @if(isset($option['count']))
                                            <span class="count">({{ $option['count'] }})</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    {{-- Multi Select --}}
                    @case('multiselect')
                        <div class="filter-options checkbox-filters">
                            @foreach($filter['options'] as $option)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="{{ $filter['code'] }}_{{ $option['value'] }}"
                                           name="{{ $filter['code'] }}" 
                                           value="{{ $option['value'] }}"
                                           data-attribute-code="{{ $filter['code'] }}">
                                    <label class="custom-control-label" 
                                           for="{{ $filter['code'] }}_{{ $option['value'] }}">
                                        {{ $option['label'] }}
                                        @if(isset($option['count']))
                                            <span class="text-muted">({{ $option['count'] }})</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @break

                    {{-- Default Dropdown --}}
                    @default
                        <select class="form-control filter-select" 
                                name="{{ $filter['code'] }}"
                                data-attribute-code="{{ $filter['code'] }}">
                            <option value="">All {{ $filter['name'] }}</option>
                            @foreach($filter['options'] as $option)
                                <option value="{{ $option['value'] }}">
                                    {{ $option['label'] }}
                                    @if(isset($option['count']))
                                        ({{ $option['count'] }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                @endswitch
            </div>
        @endforeach
    @else
        {{-- Default filters if no dynamic filters provided --}}
        
        {{-- Color Filter --}}
        <div class="filter-section mb-4">
            <h6 class="filter-heading">Color</h6>
            <div class="filter-options color-swatch-filters">
                @php
                    $colors = [
                        ['name' => 'Red', 'hex' => '#dc3545'],
                        ['name' => 'Blue', 'hex' => '#007bff'],
                        ['name' => 'Green', 'hex' => '#28a745'],
                        ['name' => 'Black', 'hex' => '#000000'],
                        ['name' => 'White', 'hex' => '#ffffff'],
                        ['name' => 'Yellow', 'hex' => '#ffc107'],
                    ];
                @endphp
                @foreach($colors as $color)
                    <label class="color-swatch-filter" title="{{ $color['name'] }}">
                        <input type="checkbox" 
                               name="color" 
                               value="{{ strtolower($color['name']) }}"
                               class="d-none"
                               data-attribute-code="color">
                        <span class="color-swatch" style="background-color: {{ $color['hex'] }};"></span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Size Filter --}}
        <div class="filter-section mb-4">
            <h6 class="filter-heading">Size</h6>
            <div class="filter-options button-filters">
                @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                    <label class="filter-option">
                        <input type="checkbox" 
                               name="size" 
                               value="{{ $size }}"
                               class="d-none"
                               data-attribute-code="size">
                        <span class="btn btn-outline-secondary btn-sm">{{ $size }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .layered-navigation {
        padding: 1.5rem;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filter-title {
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #eee;
    }

    .filter-heading {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        color: #666;
    }

    /* Color Swatch Filters */
    .color-swatch-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .color-swatch-filter {
        cursor: pointer;
        margin: 0;
        position: relative;
    }

    .color-swatch-filter .color-swatch {
        display: block;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #ddd;
        transition: all 0.2s;
    }

    .color-swatch-filter input:checked + .color-swatch {
        border-color: #333;
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #333;
    }

    .color-swatch-filter .count {
        display: none;
        position: absolute;
        top: -8px;
        right: -8px;
        background: #333;
        color: white;
        font-size: 0.65rem;
        padding: 0.1rem 0.3rem;
        border-radius: 50%;
    }

    .color-swatch-filter input:checked ~ .count {
        display: block;
    }

    /* Button Filters */
    .button-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .button-filters .filter-option {
        cursor: pointer;
        margin: 0;
    }

    .button-filters .filter-option input:checked + .btn {
        background-color: #333;
        color: white;
        border-color: #333;
    }

    /* Checkbox Filters */
    .checkbox-filters {
        max-height: 200px;
        overflow-y: auto;
    }

    .checkbox-filters .custom-checkbox {
        margin-bottom: 0.5rem;
    }

    /* Price Slider */
    .price-range-slider {
        padding: 0.5rem 0;
    }

    /* Product Card Styles */
    .product-card {
        background: #fff;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s;
        border: 1px solid #eee;
    }

    .product-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }

    .product-image {
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        transition: transform 0.3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-badges {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .quick-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .product-card:hover .quick-actions {
        opacity: 1;
    }

    .product-title a {
        color: #333;
        text-decoration: none;
    }

    .product-title a:hover {
        color: #007bff;
    }

    .sale-price {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .original-price {
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }

    .placeholder-image {
        width: 100%;
        height: 250px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }
</style>
@endpush

@push('scripts')
<script>
    // Price slider update
    document.getElementById('price-slider')?.addEventListener('input', function() {
        document.getElementById('price-value').textContent = '$' + this.value;
    });
</script>
@endpush
