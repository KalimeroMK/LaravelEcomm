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
 * @property-read Collection<int, AttributeValue>             $attributeValues
 * @property-read int|null                                    $attribute_values_count
 * @property-read Brand|null                                  $brand
 * @property-read Collection<int, Bundle>                     $bundles
 * @property-read int|null                                    $bundles_count
 * @property-read Collection<int, Cart>                       $carts
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $categories
 * @property-read MediaCollection<int, Media>                 $media
 * @property-read int|null                                    $media_count
 * @property-read Collection<int, ProductReview>              $product_reviews
 * @property-read Collection<int, Tag>                        $tags
 * @property-read int|null                                    $tags_count
 * @property-read Collection<int, Wishlist>                   $wishlists
 *
 * @method static Builder<static>|Product filter(array $filters = [])
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product whereBrandId($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereDDeal($value)
 * @method static Builder<static>|Product whereDescription($value)
 * @method static Builder<static>|Product whereDiscount($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereIsFeatured($value)
 * @method static Builder<static>|Product wherePrice($value)
 * @method static Builder<static>|Product whereSku($value)
 * @method static Builder<static>|Product whereSlug($value)
 * @method static Builder<static>|Product whereSpecialPrice($value)
 * @method static Builder<static>|Product whereSpecialPriceEnd($value)
 * @method static Builder<static>|Product whereSpecialPriceStart($value)
 * @method static Builder<static>|Product whereStatus($value)
 * @method static Builder<static>|Product whereStock($value)
 * @method static Builder<static>|Product whereSummary($value)
 * @method static Builder<static>|Product whereTitle($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Product extends Core implements HasMedia
{
    use Filterable;
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

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

    protected $casts
        = [
            'stock' => 'int',
            'price' => 'float',
            'discount' => 'float',
            'is_featured' => 'bool',
            'brand_id' => 'int',
            'special_price_start' => 'date',
            'special_price_end' => 'date',
            'special_price' => 'float',
        ];

    protected $fillable
        = [
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
            'special_price',
            'special_price_start',
            'special_price_end',
            'd_deal',
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

    /**
     * Get size attribute with fallback to null.
     */
    public function getSizeAttribute(): ?string
    {
        return $this->attributes['size'] ?? null;
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function product_reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function getReview(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->with('user')->where(
            'status',
            'active'
        )->orderBy('id', 'DESC');
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof Media) {
            return $mediaItem->getUrl();
        }

        return 'https://placehold.co/600x400@2x.png';
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof Media) {
            // Replace 'thumb' with your conversion name if needed
            return $mediaItem->getUrl('thumb');
        }

        return 'https://placehold.co/600x400@2x.png';
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getCurrentPrice(): ?float
    {
        $today = now();

        return $this->special_price && $today->between(
            $this->special_price_start,
            $this->special_price_end
        ) ? $this->special_price : null;
    }

    public function attributeValues(): self|Builder|HasMany
    {
        return $this->hasMany(AttributeValue::class, 'product_id', 'id');
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

    public function impressions(): self|HasMany
    {
        return $this->hasMany(ProductImpression::class, 'product_id');
    }

    public function clicks(): self|HasMany
    {
        return $this->hasMany(ProductClick::class, 'product_id');
    }

    /**
     * Get the product's condition attribute value.
     */
    public function getConditionAttribute(): ?string
    {
        // Find the Attribute model with code 'condition'
        $conditionAttribute = Attribute::where('code', 'condition')->first();
        if (! $conditionAttribute) {
            return null;
        }
        // Find the related AttributeValue for this product and the condition attribute
        $conditionValue = $this->attributeValues()->where('attribute_id', $conditionAttribute->id)->first();
        if (! $conditionValue) {
            return null;
        }
        // Get the value depending on the attribute type
        $column = $conditionAttribute->getValueColumnName();

        return $conditionValue->$column ?? null;
    }
}
