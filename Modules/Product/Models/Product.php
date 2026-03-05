<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalimeromk\Filterable\app\Traits\Filterable;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeFamily;
use Modules\Attribute\Models\AttributeValue;
use Modules\Billing\Models\Wishlist;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\Bundle;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;
use Modules\Tag\Models\Tag;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Product
 *
 * @property int $id
 * @property string $type (simple, configurable, variant)
 * @property int|null $parent_id
 * @property array|null $configurable_attributes
 * @property string|null $variant_name
 * @property string|null $variant_sku_suffix
 * @property int|null $attribute_family_id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $description
 * @property int $stock
 * @property string $status
 * @property float $price
 * @property float|null $discount
 * @property bool|null $is_featured
 * @property int $d_deal
 * @property int|null $brand_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property float|null $special_price
 * @property Carbon|null $special_price_start
 * @property Carbon|null $special_price_end
 * @property string|null $sku
 * @property-read Collection<int, AttributeValue> $attributeValues
 * @property-read Collection<int, AttributeValue> $allAttributeValues (including from family)
 * @property-read Product|null $parent
 * @property-read Collection<int, Product> $variants
 * @property-read Product|null $defaultVariant
 * @property-read AttributeFamily|null $attributeFamily
 * @property-read Brand|null $brand
 * @property-read Collection<int, Bundle> $bundles
 * @property-read Collection<int, Cart> $carts
 * @property-read Collection<int, Category> $categories
 * @property-read MediaCollection<int, Media> $media
 * @property-read Collection<int, ProductReview> $product_reviews
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, Wishlist> $wishlists
 *
 * @method static Builder<static>|Product filter(array $filters = [])
 * @method static Builder<static>|Product simple()
 * @method static Builder<static>|Product configurable()
 * @method static Builder<static>|Product variants()
 * @method static Builder<static>|Product filterByAttributes(array $attributes)
 * @method static Builder<static>|Product whereType($value)
 * @method static Builder<static>|Product whereParentId($value)
 *
 * @mixin Eloquent
 */
class Product extends Core implements HasMedia
{
    use Filterable;
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    public const TYPE_SIMPLE = 'simple';

    public const TYPE_CONFIGURABLE = 'configurable';

    public const TYPE_VARIANT = 'variant';

    public const likeRows = [
        'title',
        'slug',
        'summary',
        'description',
        'stock',
        'status',
        'price',
        'discount',
        'brand.title',
    ];

    protected $table = 'products';

    protected $casts = [
        'stock' => 'int',
        'price' => 'float',
        'discount' => 'float',
        'is_featured' => 'bool',
        'brand_id' => 'int',
        'attribute_family_id' => 'int',
        'parent_id' => 'int',
        'special_price_start' => 'date',
        'special_price_end' => 'date',
        'special_price' => 'float',
        'configurable_attributes' => 'array',
    ];

    protected $fillable = [
        'type',
        'title',
        'slug',
        'sku',
        'summary',
        'description',
        'stock',
        'status',
        'price',
        'discount',
        'is_featured',
        'brand_id',
        'attribute_family_id',
        'special_price',
        'special_price_start',
        'special_price_end',
        'd_deal',
        'parent_id',
        'configurable_attributes',
        'variant_name',
        'variant_sku_suffix',
    ];

    public static function Factory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public static function getProductBySlug(string $slug): ?self
    {
        return self::with(['getReview', 'categories', 'attributeValues.attribute'])
            ->whereSlug($slug)
            ->firstOrFail();
    }

    public static function countActiveProduct(): int
    {
        $data = self::whereStatus('active')->count();

        return $data ?: 0;
    }

    public static function getFeedItems(): \Illuminate\Support\Collection
    {
        return self::orderBy('created_at', 'desc')->limit(20)->get();
    }

    public function getSizeAttribute(): ?string
    {
        return $this->attributes['size'] ?? null;
    }

    // ==================== RELATIONS ====================

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function attributeFamily(): BelongsTo
    {
        return $this->belongsTo(AttributeFamily::class);
    }

    /**
     * Parent product (for variants)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Variants of a configurable product
     */
    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('type', self::TYPE_VARIANT);
    }

    /**
     * Default variant for configurable product
     */
    public function defaultVariant(): BelongsTo
    {
        return $this->belongsTo(self::class, 'default_variant_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function product_reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->with('user')
            ->where('status', 'active')
            ->orderBy('id', 'DESC');
    }

    public function getReview(): HasMany
    {
        return $this->product_reviews();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // ==================== ATTRIBUTE VALUES ====================

    /**
     * Attribute values for this product (polymorphic)
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attributable_id')
            ->where('attributable_type', self::class)
            ->orWhere(function ($query) {
                // Backward compatibility
                $query->where('product_id', $this->id)
                    ->whereNull('attributable_id');
            });
    }

    /**
     * Get attribute value by code
     */
    public function getAttributeValueByCode(string $attributeCode): mixed
    {
        $attributeValue = $this->attributeValues()
            ->whereHas('attribute', function ($q) use ($attributeCode) {
                $q->where('code', $attributeCode);
            })
            ->first();

        return $attributeValue?->getValue();
    }

    /**
     * Get the condition attribute value
     */
    public function getConditionAttribute(): ?string
    {
        return $this->getAttributeValueByCode('condition');
    }

    // ==================== CONFIGURABLE PRODUCT ====================

    /**
     * Check if this is a configurable product
     */
    public function isConfigurable(): bool
    {
        return $this->type === self::TYPE_CONFIGURABLE;
    }

    /**
     * Check if this is a variant
     */
    public function isVariant(): bool
    {
        return $this->type === self::TYPE_VARIANT;
    }

    /**
     * Check if this is a simple product
     */
    public function isSimple(): bool
    {
        return $this->type === self::TYPE_SIMPLE;
    }

    /**
     * Get configurable attributes
     */
    public function getConfigurableAttributes(): Collection
    {
        if (! $this->isConfigurable() || empty($this->configurable_attributes)) {
            return collect();
        }

        return Attribute::whereIn('code', $this->configurable_attributes)->get();
    }

    /**
     * Get all possible variant combinations
     */
    public function getVariantCombinations(): Collection
    {
        if (! $this->isConfigurable()) {
            return collect();
        }

        $attributes = $this->getConfigurableAttributes()->load('options');

        if ($attributes->isEmpty()) {
            return collect();
        }

        $combinations = collect([[]]);

        foreach ($attributes as $attribute) {
            $values = $attribute->options->pluck('value')->toArray();

            if (empty($values)) {
                continue;
            }

            $newCombinations = collect();
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombinations->push(array_merge($combination, [$attribute->code => $value]));
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Get variant by attribute combination
     */
    public function getVariantByAttributes(array $attributes): ?self
    {
        foreach ($this->variants as $variant) {
            $variantAttrs = [];
            foreach ($variant->attributeValues as $av) {
                $variantAttrs[$av->attribute->code] = $av->getValue();
            }

            if ($variantAttrs === $attributes) {
                return $variant;
            }
        }

        return null;
    }

    // ==================== SCOPES ====================

    /**
     * Scope for simple products only
     */
    public function scopeSimple(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SIMPLE);
    }

    /**
     * Scope for configurable products only
     */
    public function scopeConfigurable(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CONFIGURABLE);
    }

    /**
     * Scope for variants only
     */
    public function scopeVariants(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_VARIANT);
    }

    /**
     * Scope for non-variants (simple + configurable)
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Filter by attributes (for layered navigation)
     */
    public function scopeFilterByAttributes(Builder $query, array $filters): Builder
    {
        foreach ($filters as $attributeCode => $values) {
            if (empty($values)) {
                continue;
            }

            $query->whereHas('attributeValues.attribute', function ($q) use ($attributeCode, $values) {
                $q->where('code', $attributeCode);

                if (is_array($values)) {
                    $q->whereHas('values', function ($qv) use ($values) {
                        $qv->whereIn('text_value', $values)
                            ->orWhereIn('string_value', $values);
                    });
                } else {
                    $q->whereHas('values', function ($qv) use ($values) {
                        $qv->where('text_value', $values)
                            ->orWhere('string_value', $values);
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Filter by price range
     */
    public function scopeFilterByPrice(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    // ==================== PRICING ====================

    public function getCurrentPrice(): ?float
    {
        $today = now();

        return $this->special_price && $today->between(
            $this->special_price_start,
            $this->special_price_end
        ) ? $this->special_price : null;
    }

    /**
     * Get final price (considering special price)
     */
    public function getFinalPrice(): float
    {
        return $this->getCurrentPrice() ?? $this->price;
    }

    // ==================== MEDIA ====================

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof Media) {
            return $mediaItem->getUrl();
        }

        return route('front.placeholder.image', [
            'type' => 'product',
            'text' => \Illuminate\Support\Str::limit($this->title ?? 'Product', 20),
            'index' => $this->id ? ($this->id % 10) : 0,
        ]);
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof Media) {
            return $mediaItem->getUrl('thumb');
        }

        return $this->image_url;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }

    // ==================== STATS ====================

    public function impressions(): HasMany
    {
        return $this->hasMany(ProductImpression::class, 'product_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ProductClick::class, 'product_id');
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class)->withTimestamps();
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('categories', 'brand', 'tags');
    }

    public function searchBrandsByProduct(string $searchTerm): \Illuminate\Support\Collection
    {
        $productBrandIds = self::search($searchTerm)->get()->pluck('brand_id')->unique();

        return Brand::whereIn('id', $productBrandIds)->get();
    }

    // ==================== BOOT ====================

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate variant SKU
        static::creating(function ($product) {
            if ($product->isVariant() && $product->parent && $product->variant_sku_suffix) {
                $product->sku = $product->parent->sku.$product->variant_sku_suffix;
            }
        });
    }
}
