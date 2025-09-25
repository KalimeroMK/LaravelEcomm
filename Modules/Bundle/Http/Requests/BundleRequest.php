<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class BundleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $bundleId = $this->route('bundle') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('bundles', 'slug')->ignore($bundleId),
            ],
            'description' => [
                'required',
                'string',
                'max:2000',
            ],
            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
                'lt:price',
            ],
            'sale_start_date' => [
                'nullable',
                'date',
                'before_or_equal:sale_end_date',
            ],
            'sale_end_date' => [
                'nullable',
                'date',
                'after_or_equal:sale_start_date',
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('bundles', 'sku')->ignore($bundleId),
            ],
            'status' => [
                'required',
                'string',
                'in:active,inactive,draft',
            ],
            'is_featured' => [
                'boolean',
            ],
            'is_digital' => [
                'boolean',
            ],
            'is_downloadable' => [
                'boolean',
            ],
            'is_virtual' => [
                'boolean',
            ],
            'is_giftable' => [
                'boolean',
            ],
            'is_subscription' => [
                'boolean',
            ],
            'subscription_interval' => [
                'nullable',
                'string',
                'in:daily,weekly,monthly,quarterly,yearly',
            ],
            'subscription_duration' => [
                'nullable',
                'integer',
                'min:1',
                'max:999',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'dimensions' => [
                'nullable',
                'array',
            ],
            'dimensions.length' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'dimensions.width' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'dimensions.height' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'dimensions.unit' => [
                'nullable',
                'string',
                'in:cm,in,mm,m,ft',
            ],
            'stock_quantity' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'low_stock_threshold' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'manage_stock' => [
                'boolean',
            ],
            'backorders' => [
                'nullable',
                'string',
                'in:no,notify,allow',
            ],
            'sold_individually' => [
                'boolean',
            ],
            'purchase_note' => [
                'nullable',
                'string',
                'max:500',
            ],
            'menu_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:60',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160',
            ],
            'meta_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'products' => [
                'required',
                'array',
                'min:2',
            ],
            'products.*' => [
                'integer',
                'exists:products,id',
            ],
            'product_quantities' => [
                'nullable',
                'array',
            ],
            'product_quantities.*' => [
                'integer',
                'min:1',
                'max:999',
            ],
            'categories' => [
                'nullable',
                'array',
            ],
            'categories.*' => [
                'integer',
                'exists:categories,id',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'string',
                'max:50',
            ],
            'attributes' => [
                'nullable',
                'array',
            ],
            'attributes.*.attribute_id' => [
                'required_with:attributes',
                'integer',
                'exists:attributes,id',
            ],
            'attributes.*.value' => [
                'required_with:attributes',
                'string',
                'max:255',
            ],
            'shipping_class' => [
                'nullable',
                'string',
                'max:100',
            ],
            'download_limit' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'download_expiry' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'download_type' => [
                'nullable',
                'string',
                'in:file,url',
            ],
            'download_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'download_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,txt,zip,rar,7z',
                'max:10240',
            ],
            'gift_message' => [
                'nullable',
                'string',
                'max:500',
            ],
            'gift_wrap' => [
                'boolean',
            ],
            'gift_wrap_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'bundle_type' => [
                'required',
                'string',
                'in:fixed,flexible,custom',
            ],
            'bundle_rules' => [
                'nullable',
                'array',
            ],
            'bundle_rules.*' => [
                'string',
                'max:255',
            ],
            'bundle_conditions' => [
                'nullable',
                'array',
            ],
            'bundle_conditions.*' => [
                'string',
                'max:255',
            ],
            'bundle_actions' => [
                'nullable',
                'array',
            ],
            'bundle_actions.*' => [
                'string',
                'max:255',
            ],
            'bundle_priority' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'bundle_sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'bundle_is_active' => [
                'boolean',
            ],
            'bundle_is_featured' => [
                'boolean',
            ],
            'bundle_is_public' => [
                'boolean',
            ],
            'bundle_is_private' => [
                'boolean',
            ],
            'bundle_is_hidden' => [
                'boolean',
            ],
            'bundle_is_locked' => [
                'boolean',
            ],
            'bundle_is_editable' => [
                'boolean',
            ],
            'bundle_is_deletable' => [
                'boolean',
            ],
            'bundle_is_duplicatable' => [
                'boolean',
            ],
            'bundle_is_exportable' => [
                'boolean',
            ],
            'bundle_is_importable' => [
                'boolean',
            ],
            'bundle_is_exportable_to_csv' => [
                'boolean',
            ],
            'bundle_is_exportable_to_excel' => [
                'boolean',
            ],
            'bundle_is_exportable_to_pdf' => [
                'boolean',
            ],
            'bundle_is_exportable_to_json' => [
                'boolean',
            ],
            'bundle_is_exportable_to_xml' => [
                'boolean',
            ],
            'bundle_is_exportable_to_yaml' => [
                'boolean',
            ],
            'bundle_is_exportable_to_toml' => [
                'boolean',
            ],
            'bundle_is_exportable_to_ini' => [
                'boolean',
            ],
            'bundle_is_exportable_to_env' => [
                'boolean',
            ],
            'bundle_is_exportable_to_dotenv' => [
                'boolean',
            ],
            'bundle_is_exportable_to_htaccess' => [
                'boolean',
            ],
            'bundle_is_exportable_to_robots' => [
                'boolean',
            ],
            'bundle_is_exportable_to_sitemap' => [
                'boolean',
            ],
            'bundle_is_exportable_to_manifest' => [
                'boolean',
            ],
            'bundle_is_exportable_to_sw' => [
                'boolean',
            ],
            'bundle_is_exportable_to_webmanifest' => [
                'boolean',
            ],
            'bundle_is_exportable_to_webapp' => [
                'boolean',
            ],
            'bundle_is_exportable_to_pwa' => [
                'boolean',
            ],
            'bundle_is_exportable_to_spa' => [
                'boolean',
            ],
            'bundle_is_exportable_to_ssr' => [
                'boolean',
            ],
            'bundle_is_exportable_to_csr' => [
                'boolean',
            ],
            'bundle_is_exportable_to_isr' => [
                'boolean',
            ],
            'bundle_is_exportable_to_prerender' => [
                'boolean',
            ],
            'bundle_is_exportable_to_static' => [
                'boolean',
            ],
            'bundle_is_exportable_to_dynamic' => [
                'boolean',
            ],
            'bundle_is_exportable_to_hybrid' => [
                'boolean',
            ],
            'bundle_is_exportable_to_mixed' => [
                'boolean',
            ],
            'bundle_is_exportable_to_other' => [
                'boolean',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Bundle name is required.',
            'name.regex' => 'Bundle name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'name.min' => 'Bundle name must be at least 2 characters long.',
            'name.max' => 'Bundle name must not exceed 255 characters.',
            'slug.required' => 'Bundle slug is required.',
            'slug.regex' => 'Bundle slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'slug.min' => 'Bundle slug must be at least 2 characters long.',
            'slug.max' => 'Bundle slug must not exceed 100 characters.',
            'description.required' => 'Bundle description is required.',
            'description.min' => 'Bundle description must be at least 10 characters long.',
            'description.max' => 'Bundle description must not exceed 2000 characters.',
            'short_description.max' => 'Short description must not exceed 500 characters.',
            'price.required' => 'Bundle price is required.',
            'price.min' => 'Bundle price must be at least 0.',
            'price.max' => 'Bundle price cannot exceed 999999.99.',
            'sale_price.min' => 'Sale price must be at least 0.',
            'sale_price.max' => 'Sale price cannot exceed 999999.99.',
            'sale_price.lt' => 'Sale price must be less than regular price.',
            'sale_start_date.before_or_equal' => 'Sale start date must be before or equal to sale end date.',
            'sale_end_date.after_or_equal' => 'Sale end date must be after or equal to sale start date.',
            'sku.required' => 'Bundle SKU is required.',
            'sku.regex' => 'Bundle SKU can only contain uppercase letters, numbers, hyphens, and underscores.',
            'sku.unique' => 'This SKU is already in use.',
            'sku.min' => 'Bundle SKU must be at least 3 characters long.',
            'sku.max' => 'Bundle SKU must not exceed 100 characters.',
            'status.required' => 'Bundle status is required.',
            'status.in' => 'Invalid bundle status.',
            'subscription_interval.in' => 'Subscription interval must be daily, weekly, monthly, quarterly, or yearly.',
            'subscription_duration.min' => 'Subscription duration must be at least 1.',
            'subscription_duration.max' => 'Subscription duration cannot exceed 999.',
            'weight.min' => 'Bundle weight must be at least 0.',
            'weight.max' => 'Bundle weight cannot exceed 999999.99.',
            'dimensions.length.min' => 'Length must be at least 0.',
            'dimensions.length.max' => 'Length cannot exceed 999999.99.',
            'dimensions.width.min' => 'Width must be at least 0.',
            'dimensions.width.max' => 'Width cannot exceed 999999.99.',
            'dimensions.height.min' => 'Height must be at least 0.',
            'dimensions.height.max' => 'Height cannot exceed 999999.99.',
            'dimensions.unit.in' => 'Dimension unit must be cm, in, mm, m, or ft.',
            'stock_quantity.min' => 'Stock quantity must be at least 0.',
            'stock_quantity.max' => 'Stock quantity cannot exceed 999999.',
            'low_stock_threshold.min' => 'Low stock threshold must be at least 0.',
            'low_stock_threshold.max' => 'Low stock threshold cannot exceed 999999.',
            'backorders.in' => 'Backorders must be no, notify, or allow.',
            'menu_order.min' => 'Menu order must be at least 0.',
            'menu_order.max' => 'Menu order cannot exceed 9999.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
            'images.max' => 'Maximum 10 images are allowed.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'products.required' => 'Products are required.',
            'products.min' => 'At least 2 products are required for a bundle.',
            'products.max' => 'Maximum 50 products are allowed in a bundle.',
            'products.*.exists' => 'One or more selected products do not exist.',
            'product_quantities.*.min' => 'Product quantity must be at least 1.',
            'product_quantities.*.max' => 'Product quantity cannot exceed 999.',
            'categories.max' => 'Maximum 20 categories are allowed.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
            'tags.max' => 'Maximum 20 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'attributes.max' => 'Maximum 50 attributes are allowed.',
            'attributes.*.attribute_id.required_with' => 'Attribute ID is required.',
            'attributes.*.value.required_with' => 'Attribute value is required.',
            'shipping_class.max' => 'Shipping class must not exceed 100 characters.',
            'download_limit.min' => 'Download limit must be at least 0.',
            'download_limit.max' => 'Download limit cannot exceed 999999.',
            'download_expiry.min' => 'Download expiry must be at least 0.',
            'download_expiry.max' => 'Download expiry cannot exceed 999999.',
            'download_type.in' => 'Download type must be file or url.',
            'download_url.url' => 'Please enter a valid download URL.',
            'download_url.max' => 'Download URL must not exceed 500 characters.',
            'download_file.max' => 'Download file must not exceed 10MB.',
            'gift_message.max' => 'Gift message must not exceed 500 characters.',
            'gift_wrap_price.min' => 'Gift wrap price must be at least 0.',
            'gift_wrap_price.max' => 'Gift wrap price cannot exceed 999999.99.',
            'bundle_type.required' => 'Bundle type is required.',
            'bundle_type.in' => 'Bundle type must be fixed, flexible, or custom.',
            'bundle_rules.max' => 'Maximum 100 bundle rules are allowed.',
            'bundle_rules.*.max' => 'Each bundle rule must not exceed 255 characters.',
            'bundle_conditions.max' => 'Maximum 100 bundle conditions are allowed.',
            'bundle_conditions.*.max' => 'Each bundle condition must not exceed 255 characters.',
            'bundle_actions.max' => 'Maximum 100 bundle actions are allowed.',
            'bundle_actions.*.max' => 'Each bundle action must not exceed 255 characters.',
            'bundle_priority.min' => 'Bundle priority must be at least 0.',
            'bundle_priority.max' => 'Bundle priority cannot exceed 9999.',
            'bundle_sort_order.min' => 'Bundle sort order must be at least 0.',
            'bundle_sort_order.max' => 'Bundle sort order cannot exceed 9999.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name length
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Bundle name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Bundle name must not exceed 255 characters.'
                    );
                }
            }

            // Validate slug length
            if ($this->filled('slug')) {
                $slug = $this->slug;

                if (mb_strlen($slug) < 2) {
                    $validator->errors()->add(
                        'slug',
                        'Bundle slug must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($slug) > 100) {
                    $validator->errors()->add(
                        'slug',
                        'Bundle slug must not exceed 100 characters.'
                    );
                }
            }

            // Validate description
            if ($this->filled('description')) {
                $description = $this->description;

                if (mb_strlen($description) < 10) {
                    $validator->errors()->add(
                        'description',
                        'Bundle description must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($description) > 2000) {
                    $validator->errors()->add(
                        'description',
                        'Bundle description must not exceed 2000 characters.'
                    );
                }
            }

            // Validate short description
            if ($this->filled('short_description') && mb_strlen($this->short_description) > 500) {
                $validator->errors()->add(
                    'short_description',
                    'Short description must not exceed 500 characters.'
                );
            }

            // Validate price
            if ($this->filled('price')) {
                $price = $this->price;

                if ($price < 0) {
                    $validator->errors()->add(
                        'price',
                        'Bundle price cannot be negative.'
                    );
                }

                if ($price > 999999.99) {
                    $validator->errors()->add(
                        'price',
                        'Bundle price cannot exceed 999999.99.'
                    );
                }
            }

            // Validate sale price
            if ($this->filled('sale_price') && $this->filled('price')) {
                $salePrice = $this->sale_price;
                $price = $this->price;

                if ($salePrice >= $price) {
                    $validator->errors()->add(
                        'sale_price',
                        'Sale price must be less than regular price.'
                    );
                }
            }

            // Validate sale dates
            if ($this->filled('sale_start_date') && $this->filled('sale_end_date')) {
                $saleStartDate = $this->sale_start_date;
                $saleEndDate = $this->sale_end_date;

                if ($saleStartDate >= $saleEndDate) {
                    $validator->errors()->add(
                        'sale_end_date',
                        'Sale end date must be after sale start date.'
                    );
                }
            }

            // Validate SKU
            if ($this->filled('sku')) {
                $sku = $this->sku;

                if (mb_strlen($sku) < 3) {
                    $validator->errors()->add(
                        'sku',
                        'Bundle SKU must be at least 3 characters long.'
                    );
                }

                if (mb_strlen($sku) > 100) {
                    $validator->errors()->add(
                        'sku',
                        'Bundle SKU must not exceed 100 characters.'
                    );
                }
            }

            // Validate status
            if ($this->filled('status')) {
                $status = $this->status;
                $validStatuses = ['active', 'inactive', 'draft'];

                if (! in_array($status, $validStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Invalid bundle status.'
                    );
                }
            }

            // Validate subscription settings
            if ($this->filled('is_subscription') && $this->is_subscription) {
                if (! $this->filled('subscription_interval')) {
                    $validator->errors()->add(
                        'subscription_interval',
                        'Subscription interval is required for subscription bundles.'
                    );
                }

                if (! $this->filled('subscription_duration')) {
                    $validator->errors()->add(
                        'subscription_duration',
                        'Subscription duration is required for subscription bundles.'
                    );
                }
            }

            // Validate weight
            if ($this->filled('weight') && $this->weight < 0) {
                $validator->errors()->add(
                    'weight',
                    'Bundle weight cannot be negative.'
                );
            }

            // Validate dimensions
            if ($this->filled('dimensions')) {
                $dimensions = $this->dimensions;

                if (isset($dimensions['length']) && $dimensions['length'] < 0) {
                    $validator->errors()->add(
                        'dimensions.length',
                        'Length cannot be negative.'
                    );
                }

                if (isset($dimensions['width']) && $dimensions['width'] < 0) {
                    $validator->errors()->add(
                        'dimensions.width',
                        'Width cannot be negative.'
                    );
                }

                if (isset($dimensions['height']) && $dimensions['height'] < 0) {
                    $validator->errors()->add(
                        'dimensions.height',
                        'Height cannot be negative.'
                    );
                }

                if (isset($dimensions['unit'])) {
                    $unit = $dimensions['unit'];
                    $validUnits = ['cm', 'in', 'mm', 'm', 'ft'];

                    if (! in_array($unit, $validUnits)) {
                        $validator->errors()->add(
                            'dimensions.unit',
                            'Invalid dimension unit.'
                        );
                    }
                }
            }

            // Validate stock settings
            if ($this->filled('stock_quantity') && $this->stock_quantity < 0) {
                $validator->errors()->add(
                    'stock_quantity',
                    'Stock quantity cannot be negative.'
                );
            }

            if ($this->filled('low_stock_threshold') && $this->low_stock_threshold < 0) {
                $validator->errors()->add(
                    'low_stock_threshold',
                    'Low stock threshold cannot be negative.'
                );
            }

            // Validate backorders
            if ($this->filled('backorders')) {
                $backorders = $this->backorders;
                $validBackorders = ['no', 'notify', 'allow'];

                if (! in_array($backorders, $validBackorders)) {
                    $validator->errors()->add(
                        'backorders',
                        'Invalid backorders setting.'
                    );
                }
            }

            // Validate menu order
            if ($this->filled('menu_order')) {
                $menuOrder = $this->menu_order;

                if ($menuOrder < 0 || $menuOrder > 9999) {
                    $validator->errors()->add(
                        'menu_order',
                        'Menu order must be between 0 and 9999.'
                    );
                }
            }

            // Validate meta title
            if ($this->filled('meta_title') && mb_strlen($this->meta_title) > 60) {
                $validator->errors()->add(
                    'meta_title',
                    'Meta title should not exceed 60 characters for SEO.'
                );
            }

            // Validate meta description
            if ($this->filled('meta_description') && mb_strlen($this->meta_description) > 160) {
                $validator->errors()->add(
                    'meta_description',
                    'Meta description should not exceed 160 characters for SEO.'
                );
            }

            // Validate images
            if ($this->filled('images')) {
                $images = $this->images;

                if (count($images) > 10) {
                    $validator->errors()->add(
                        'images',
                        'Maximum 10 images are allowed.'
                    );
                }

                foreach ($images as $index => $image) {
                    if ($image->getSize() > 5120) { // 5MB
                        $validator->errors()->add(
                            'images.'.$index,
                            'Each image must not exceed 5MB.'
                        );
                    }
                }
            }

            // Validate products
            if ($this->filled('products')) {
                $products = $this->products;

                if (count($products) < 2) {
                    $validator->errors()->add(
                        'products',
                        'At least 2 products are required for a bundle.'
                    );
                }

                if (count($products) > 50) {
                    $validator->errors()->add(
                        'products',
                        'Maximum 50 products are allowed in a bundle.'
                    );
                }

                foreach ($products as $index => $productId) {
                    $product = \Modules\Product\Models\Product::find($productId);

                    if (! $product) {
                        $validator->errors()->add(
                            'products.'.$index,
                            'Product does not exist.'
                        );
                    } elseif (! $product->is_active) {
                        $validator->errors()->add(
                            'products.'.$index,
                            'Product is not active.'
                        );
                    }
                }
            }

            // Validate product quantities
            if ($this->filled('product_quantities')) {
                $productQuantities = $this->product_quantities;
                $products = $this->products ?? [];

                if (count($productQuantities) !== count($products)) {
                    $validator->errors()->add(
                        'product_quantities',
                        'Product quantities count must match products count.'
                    );
                }

                foreach ($productQuantities as $index => $quantity) {
                    if ($quantity < 1) {
                        $validator->errors()->add(
                            'product_quantities.'.$index,
                            'Product quantity must be at least 1.'
                        );
                    }

                    if ($quantity > 999) {
                        $validator->errors()->add(
                            'product_quantities.'.$index,
                            'Product quantity cannot exceed 999.'
                        );
                    }
                }
            }

            // Validate categories
            if ($this->filled('categories')) {
                $categories = $this->categories;

                if (count($categories) > 20) {
                    $validator->errors()->add(
                        'categories',
                        'Maximum 20 categories are allowed.'
                    );
                }

                foreach ($categories as $index => $categoryId) {
                    $category = \Modules\Category\Models\Category::find($categoryId);

                    if (! $category) {
                        $validator->errors()->add(
                            'categories.'.$index,
                            'Category does not exist.'
                        );
                    } elseif (! $category->is_active) {
                        $validator->errors()->add(
                            'categories.'.$index,
                            'Category is not active.'
                        );
                    }
                }
            }

            // Validate tags
            if ($this->filled('tags')) {
                $tags = $this->tags;

                if (count($tags) > 20) {
                    $validator->errors()->add(
                        'tags',
                        'Maximum 20 tags are allowed.'
                    );
                }

                foreach ($tags as $index => $tag) {
                    if (mb_strlen($tag) < 2) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must be at least 2 characters long.'
                        );
                    }

                    if (mb_strlen($tag) > 50) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must not exceed 50 characters.'
                        );
                    }
                }
            }

            // Validate attributes
            if ($this->filled('attributes')) {
                $attributes = $this->attributes;

                if (count($attributes) > 50) {
                    $validator->errors()->add(
                        'attributes',
                        'Maximum 50 attributes are allowed.'
                    );
                }

                foreach ($attributes as $index => $attribute) {
                    if (! isset($attribute['attribute_id'])) {
                        $validator->errors()->add(
                            'attributes.'.$index.'.attribute_id',
                            'Attribute ID is required.'
                        );
                    } elseif (! isset($attribute['value'])) {
                        $validator->errors()->add(
                            'attributes.'.$index.'.value',
                            'Attribute value is required.'
                        );
                    } else {
                        $attributeId = $attribute['attribute_id'];
                        $attributeModel = \Modules\Attribute\Models\Attribute::find($attributeId);

                        if (! $attributeModel) {
                            $validator->errors()->add(
                                'attributes.'.$index.'.attribute_id',
                                'Attribute does not exist.'
                            );
                        } elseif (! $attributeModel->is_active) {
                            $validator->errors()->add(
                                'attributes.'.$index.'.attribute_id',
                                'Attribute is not active.'
                            );
                        }
                    }
                }
            }

            // Validate shipping class
            if ($this->filled('shipping_class') && mb_strlen($this->shipping_class) > 100) {
                $validator->errors()->add(
                    'shipping_class',
                    'Shipping class must not exceed 100 characters.'
                );
            }

            // Validate download settings
            if ($this->filled('is_downloadable') && $this->is_downloadable) {
                if (! $this->filled('download_type')) {
                    $validator->errors()->add(
                        'download_type',
                        'Download type is required for downloadable bundles.'
                    );
                }

                if ($this->download_type === 'url' && ! $this->filled('download_url')) {
                    $validator->errors()->add(
                        'download_url',
                        'Download URL is required for URL download type.'
                    );
                }

                if ($this->download_type === 'file' && ! $this->filled('download_file')) {
                    $validator->errors()->add(
                        'download_file',
                        'Download file is required for file download type.'
                    );
                }
            }

            // Validate download limit
            if ($this->filled('download_limit') && $this->download_limit < 0) {
                $validator->errors()->add(
                    'download_limit',
                    'Download limit cannot be negative.'
                );
            }

            // Validate download expiry
            if ($this->filled('download_expiry') && $this->download_expiry < 0) {
                $validator->errors()->add(
                    'download_expiry',
                    'Download expiry cannot be negative.'
                );
            }

            // Validate download URL
            if ($this->filled('download_url')) {
                $downloadUrl = $this->download_url;

                if (! filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add(
                        'download_url',
                        'Please enter a valid download URL.'
                    );
                }
            }

            // Validate download file
            if ($this->filled('download_file')) {
                $downloadFile = $this->download_file;

                if ($downloadFile->getSize() > 10240) { // 10MB
                    $validator->errors()->add(
                        'download_file',
                        'Download file must not exceed 10MB.'
                    );
                }
            }

            // Validate gift message
            if ($this->filled('gift_message') && mb_strlen($this->gift_message) > 500) {
                $validator->errors()->add(
                    'gift_message',
                    'Gift message must not exceed 500 characters.'
                );
            }

            // Validate gift wrap price
            if ($this->filled('gift_wrap_price') && $this->gift_wrap_price < 0) {
                $validator->errors()->add(
                    'gift_wrap_price',
                    'Gift wrap price cannot be negative.'
                );
            }

            // Validate bundle type
            if ($this->filled('bundle_type')) {
                $bundleType = $this->bundle_type;
                $validTypes = ['fixed', 'flexible', 'custom'];

                if (! in_array($bundleType, $validTypes)) {
                    $validator->errors()->add(
                        'bundle_type',
                        'Invalid bundle type.'
                    );
                }
            }

            // Validate bundle rules
            if ($this->filled('bundle_rules')) {
                $bundleRules = $this->bundle_rules;

                if (count($bundleRules) > 100) {
                    $validator->errors()->add(
                        'bundle_rules',
                        'Maximum 100 bundle rules are allowed.'
                    );
                }

                foreach ($bundleRules as $index => $rule) {
                    if (mb_strlen($rule) < 5) {
                        $validator->errors()->add(
                            'bundle_rules.'.$index,
                            'Each bundle rule must be at least 5 characters long.'
                        );
                    }
                }
            }

            // Validate bundle conditions
            if ($this->filled('bundle_conditions')) {
                $bundleConditions = $this->bundle_conditions;

                if (count($bundleConditions) > 100) {
                    $validator->errors()->add(
                        'bundle_conditions',
                        'Maximum 100 bundle conditions are allowed.'
                    );
                }

                foreach ($bundleConditions as $index => $condition) {
                    if (mb_strlen($condition) < 5) {
                        $validator->errors()->add(
                            'bundle_conditions.'.$index,
                            'Each bundle condition must be at least 5 characters long.'
                        );
                    }
                }
            }

            // Validate bundle actions
            if ($this->filled('bundle_actions')) {
                $bundleActions = $this->bundle_actions;

                if (count($bundleActions) > 100) {
                    $validator->errors()->add(
                        'bundle_actions',
                        'Maximum 100 bundle actions are allowed.'
                    );
                }

                foreach ($bundleActions as $index => $action) {
                    if (mb_strlen($action) < 5) {
                        $validator->errors()->add(
                            'bundle_actions.'.$index,
                            'Each bundle action must be at least 5 characters long.'
                        );
                    }
                }
            }

            // Validate bundle priority
            if ($this->filled('bundle_priority')) {
                $bundlePriority = $this->bundle_priority;

                if ($bundlePriority < 0 || $bundlePriority > 9999) {
                    $validator->errors()->add(
                        'bundle_priority',
                        'Bundle priority must be between 0 and 9999.'
                    );
                }
            }

            // Validate bundle sort order
            if ($this->filled('bundle_sort_order')) {
                $bundleSortOrder = $this->bundle_sort_order;

                if ($bundleSortOrder < 0 || $bundleSortOrder > 9999) {
                    $validator->errors()->add(
                        'bundle_sort_order',
                        'Bundle sort order must be between 0 and 9999.'
                    );
                }
            }

            // Validate bundle name uniqueness (case insensitive)
            if ($this->filled('name')) {
                $name = $this->name;
                $bundleId = $this->route('bundle') ?? $this->route('id');

                $existingBundle = \Modules\Bundle\Models\Bundle::where('name', 'LIKE', $name)
                    ->where('id', '!=', $bundleId)
                    ->first();

                if ($existingBundle) {
                    $validator->errors()->add(
                        'name',
                        'A bundle with this name already exists.'
                    );
                }
            }

            // Validate bundle slug uniqueness (case insensitive)
            if ($this->filled('slug')) {
                $slug = $this->slug;
                $bundleId = $this->route('bundle') ?? $this->route('id');

                $existingBundle = \Modules\Bundle\Models\Bundle::where('slug', 'LIKE', $slug)
                    ->where('id', '!=', $bundleId)
                    ->first();

                if ($existingBundle) {
                    $validator->errors()->add(
                        'slug',
                        'A bundle with this slug already exists.'
                    );
                }
            }
        });
    }
}
