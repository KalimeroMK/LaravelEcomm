<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalimeromk\Filterable\app\Traits\Filterable;
use Modules\Admin\Models\Condition;
use Modules\Attribute\Models\AttributeValue;
use Modules\Billing\Models\Wishlist;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\Bundle;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Tag\Models\Tag;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Product
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $description
 * @property int $stock
 * @property int $d_deal
 * @property string $condition
 * @property string $status
 * @property float $price
 * @property float $special_price
 * @property float $discount
 * @property bool $is_featured
 * @property Carbon|null $special_price_start
 * @property Carbon|null $special_price_end
 * @property int|null $brand_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Brand|null $brand
 * @property Collection|Cart[] $carts
 * @property Collection|ProductReview[] $product_reviews
 * @property Collection|Wishlist[] $wishlists
 * @property-read int|null                                $carts_count
 * @property-read int|null                                $product_reviews_count
 * @property-read int|null                                $wishlists_count
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $categories
 * @property-read int|null                                $categories_count
 * @property-read string                                  $image_url

 *
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBrandId($value)
 * @method static Builder|Product whereCondition($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereDiscount($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsFeatured($value)
 * @method static Builder|Product wherePhoto($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereSlug($value)
 * @method static Builder|Product whereStatus($value)
 * @method static Builder|Product whereStock($value)
 * @method static Builder|Product whereSummary($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Product extends Core implements HasMedia
{
    use Filterable;
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    public const likeRows
        = [
            'title',
            'slug',
            'summary',
            'description',
            'stock',

            'condition.status',
            'status',
            'price',
            'discount',
            'brand.title',
            'color',
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
            'condition_id' => 'int',
        ];

    protected $fillable
        = [
            'title',
            'slug',
            'sku',
            'summary',
            'description',
            'stock',
            'condition_id',
            'status',
            'price',
            'discount',
            'is_featured',
            'brand_id',
            'color',
            'special_price',
            'special_price_start',
            'special_price_end',
            'condition_id',
        ];

    public static function Factory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public static function getProductBySlug(string $slug): ?self
    {
        return self::with(['getReview', 'categories'])->whereSlug($slug)->firstOrFail();
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
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->with('user')->where('status',
            'active')->orderBy('id', 'DESC');
    }

    public function incrementSlug(string $slug): string
    {
        $original = $slug;
        $count = 2;
        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-".$count++;
        }

        return $slug;
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media) {
            return $mediaItem->getUrl();
        }

        return 'https://placehold.co/600x400@2x.png';
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        if ($mediaItem instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media) {
            // Replace 'thumb' with your conversion name if needed
            return $mediaItem->getUrl('thumb');
        }

        return 'https://placehold.co/600x400@2x.png';
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getCurrentPrice(): mixed
    {
        $today = now();

        return $this->special_price && $today->between($this->special_price_start,
            $this->special_price_end) ? $this->special_price : null;
    }

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'product_id', 'id');
    }

    public function attributes()
    {
        return $this->belongsToMany(\Modules\Attribute\Models\Attribute::class, 'attribute_product', 'product_id', 'attribute_id');
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class)->withTimestamps();
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('categories', 'brand', 'condition');
    }

    public function searchBrandsByProduct(string $searchTerm): \Illuminate\Support\Collection
    {
        $productBrandIds = self::search($searchTerm)->get()->pluck('brand_id')->unique();

        return Brand::whereIn('id', $productBrandIds)->get();
    }
}
