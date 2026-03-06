<form method="POST"
      action="{{ route($coupon->exists ? 'coupons.update' : 'coupons.store', $coupon->exists ? $coupon->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($coupon->exists)
        @method('put')
    @endif

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <h5>@lang('coupon.basic_info')</h5>
            
            <div class="form-group">
                <label for="code">@lang('coupon.code') <span class="text-danger">*</span></label>
                <input id="code" type="text" name="code" placeholder="e.g., SAVE20" 
                       value="{{ old('code', $coupon->code) }}" class="form-control" required>
                <small class="form-text text-muted">@lang('coupon.code_help')</small>
            </div>

            <div class="form-group">
                <label for="name">@lang('coupon.name')</label>
                <input id="name" type="text" name="name" placeholder="e.g., Winter Sale 2024" 
                       value="{{ old('name', $coupon->name) }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="description">@lang('coupon.description')</label>
                <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="type">@lang('coupon.type') <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-control" required onchange="toggleCouponValue()">
                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>@lang('coupon.type_fixed')</option>
                    <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>@lang('coupon.type_percent')</option>
                    <option value="free_shipping" {{ old('type', $coupon->type) == 'free_shipping' ? 'selected' : '' }}>@lang('coupon.type_free_shipping')</option>
                </select>
            </div>

            <div class="form-group" id="value-group">
                <label for="value">@lang('coupon.value') <span class="text-danger">*</span></label>
                <input id="value" type="number" name="value" step="0.01" 
                       value="{{ old('value', $coupon->value) }}" class="form-control">
                <small class="form-text text-muted" id="value-help">@lang('coupon.value_help')</small>
            </div>
        </div>

        <!-- Restrictions -->
        <div class="col-md-6">
            <h5>@lang('coupon.restrictions')</h5>

            <div class="form-group">
                <label for="minimum_amount">@lang('coupon.minimum_amount')</label>
                <input id="minimum_amount" type="number" name="minimum_amount" step="0.01" 
                       value="{{ old('minimum_amount', $coupon->minimum_amount) }}" class="form-control">
                <small class="form-text text-muted">@lang('coupon.minimum_amount_help')</small>
            </div>

            <div class="form-group" id="max-discount-group">
                <label for="maximum_discount">@lang('coupon.maximum_discount')</label>
                <input id="maximum_discount" type="number" name="maximum_discount" step="0.01" 
                       value="{{ old('maximum_discount', $coupon->maximum_discount) }}" class="form-control">
                <small class="form-text text-muted">@lang('coupon.maximum_discount_help')</small>
            </div>

            <div class="form-group">
                <label for="usage_limit">@lang('coupon.usage_limit')</label>
                <input id="usage_limit" type="number" name="usage_limit" 
                       value="{{ old('usage_limit', $coupon->usage_limit) }}" class="form-control">
                <small class="form-text text-muted">@lang('coupon.usage_limit_help')</small>
            </div>

            <div class="form-group">
                <label for="usage_limit_per_user">@lang('coupon.usage_limit_per_user')</label>
                <input id="usage_limit_per_user" type="number" name="usage_limit_per_user" 
                       value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" class="form-control">
                <small class="form-text text-muted">@lang('coupon.usage_limit_per_user_help')</small>
            </div>

            @if($coupon->exists)
            <div class="form-group">
                <label>@lang('coupon.times_used')</label>
                <input type="text" value="{{ $coupon->times_used }}" class="form-control" disabled>
            </div>
            @endif
        </div>
    </div>

    <hr>

    <div class="row">
        <!-- Date Range -->
        <div class="col-md-6">
            <h5>@lang('coupon.date_range')</h5>

            <div class="form-group">
                <label for="starts_at">@lang('coupon.starts_at')</label>
                <input id="starts_at" type="datetime-local" name="starts_at" 
                       value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}" 
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="expires_at">@lang('coupon.expires_at')</label>
                <input id="expires_at" type="datetime-local" name="expires_at" 
                       value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" 
                       class="form-control">
            </div>
        </div>

        <!-- Status & Settings -->
        <div class="col-md-6">
            <h5>@lang('coupon.settings')</h5>

            <div class="form-group">
                <label for="status">@lang('coupon.status')</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>@lang('coupon.status_active')</option>
                    <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>@lang('coupon.status_inactive')</option>
                </select>
            </div>

            <div class="form-check">
                <input type="checkbox" name="is_public" id="is_public" value="1" class="form-check-input" 
                       {{ old('is_public', $coupon->is_public) ? 'checked' : '' }}>
                <label for="is_public" class="form-check-label">@lang('coupon.is_public')</label>
                <small class="form-text text-muted d-block">@lang('coupon.is_public_help')</small>
            </div>

            <div class="form-check mt-2">
                <input type="checkbox" name="is_stackable" id="is_stackable" value="1" class="form-check-input" 
                       {{ old('is_stackable', $coupon->is_stackable) ? 'checked' : '' }}>
                <label for="is_stackable" class="form-check-label">@lang('coupon.is_stackable')</label>
                <small class="form-text text-muted d-block">@lang('coupon.is_stackable_help')</small>
            </div>

            <div class="form-check mt-2">
                <input type="checkbox" name="free_shipping" id="free_shipping" value="1" class="form-check-input" 
                       {{ old('free_shipping', $coupon->free_shipping) ? 'checked' : '' }}>
                <label for="free_shipping" class="form-check-label">@lang('coupon.free_shipping')</label>
                <small class="form-text text-muted d-block">@lang('coupon.free_shipping_help')</small>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <!-- Product Restrictions -->
        <div class="col-md-6">
            <h5>@lang('coupon.product_restrictions')</h5>

            <div class="form-group">
                <label for="applicable_products">@lang('coupon.applicable_products')</label>
                <select name="applicable_products[]" id="applicable_products" class="form-control select2" multiple>
                    @foreach(\Modules\Product\Models\Product::all() as $product)
                        <option value="{{ $product->id }}" 
                            {{ in_array($product->id, old('applicable_products', $coupon->applicable_products ?? [])) ? 'selected' : '' }}>
                            {{ $product->title }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">@lang('coupon.applicable_products_help')</small>
            </div>

            <div class="form-group">
                <label for="applicable_categories">@lang('coupon.applicable_categories')</label>
                <select name="applicable_categories[]" id="applicable_categories" class="form-control select2" multiple>
                    @foreach(\Modules\Category\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" 
                            {{ in_array($category->id, old('applicable_categories', $coupon->applicable_categories ?? [])) ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="applicable_brands">@lang('coupon.applicable_brands')</label>
                <select name="applicable_brands[]" id="applicable_brands" class="form-control select2" multiple>
                    @foreach(\Modules\Brand\Models\Brand::all() as $brand)
                        <option value="{{ $brand->id }}" 
                            {{ in_array($brand->id, old('applicable_brands', $coupon->applicable_brands ?? [])) ? 'selected' : '' }}>
                            {{ $brand->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Exclusions -->
        <div class="col-md-6">
            <h5>@lang('coupon.exclusions')</h5>

            <div class="form-group">
                <label for="excluded_products">@lang('coupon.excluded_products')</label>
                <select name="excluded_products[]" id="excluded_products" class="form-control select2" multiple>
                    @foreach(\Modules\Product\Models\Product::all() as $product)
                        <option value="{{ $product->id }}" 
                            {{ in_array($product->id, old('excluded_products', $coupon->excluded_products ?? [])) ? 'selected' : '' }}>
                            {{ $product->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="excluded_categories">@lang('coupon.excluded_categories')</label>
                <select name="excluded_categories[]" id="excluded_categories" class="form-control select2" multiple>
                    @foreach(\Modules\Category\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" 
                            {{ in_array($category->id, old('excluded_categories', $coupon->excluded_categories ?? [])) ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="excluded_brands">@lang('coupon.excluded_brands')</label>
                <select name="excluded_brands[]" id="excluded_brands" class="form-control select2" multiple>
                    @foreach(\Modules\Brand\Models\Brand::all() as $brand)
                        <option value="{{ $brand->id }}" 
                            {{ in_array($brand->id, old('excluded_brands', $coupon->excluded_brands ?? [])) ? 'selected' : '' }}>
                            {{ $brand->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-group mb-3 mt-4">
        <button type="submit" class="btn btn-success">
            {{ $coupon->exists ? __('coupon.update') : __('coupon.create') }}
        </button>
        <a href="{{ route('coupons.index') }}" class="btn btn-secondary">@lang('coupon.cancel')</a>
    </div>
</form>

@push('scripts')
<script>
    function toggleCouponValue() {
        const type = document.getElementById('type').value;
        const valueGroup = document.getElementById('value-group');
        const maxDiscountGroup = document.getElementById('max-discount-group');
        const valueHelp = document.getElementById('value-help');
        
        if (type === 'free_shipping') {
            valueGroup.style.display = 'none';
            document.getElementById('value').value = '0';
        } else {
            valueGroup.style.display = 'block';
            if (type === 'percent') {
                valueHelp.textContent = '{{ __("coupon.value_help_percent") }}';
                maxDiscountGroup.style.display = 'block';
            } else {
                valueHelp.textContent = '{{ __("coupon.value_help_fixed") }}';
                maxDiscountGroup.style.display = 'none';
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleCouponValue();
        
        // Initialize Select2 for multi-selects
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: '{{ __("coupon.select_products") }}',
                allowClear: true
            });
        }
    });
</script>
@endpush
