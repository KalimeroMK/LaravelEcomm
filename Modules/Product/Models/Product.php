<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Product\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Kalimeromk\Filterable\app\Traits\Filterable;
use Laravel\Scout\Searchable;
use Modules\Attribute\Models\AttributeValue;
use Modules\Billing\Models\Wishlist;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\Bundle;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Core\Helpers\Condition;
use Modules\Core\Models\Core;
use Modules\Size\Models\Size;
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
 * @property string|null $size
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
 * @package App\Models
 * @property-read int|null $carts_count
 * @property-read int|null $product_reviews_count
 * @property-read int|null $wishlists_count
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
 * @method static Builder|Product whereSize($value)
 * @method static Builder|Product whereSlug($value)
 * @method static Builder|Product whereStatus($value)
 * @method static Builder|Product whereStock($value)
 * @method static Builder|Product whereSummary($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read string $image_url
 * @property string|null $color
 * @method static Builder|Product whereColor($value)
 */
class Product extends Core implements HasMedia, Explored, IndexSettings, Aliased
{
    use HasFactory;
    use Filterable;
    use InteractsWithMedia;
    use Searchable;

    protected $table = 'products';

    protected $casts = [
        'stock' => 'int',
        'price' => 'float',
        'discount' => 'float',
        'is_featured' => 'bool',
        'brand_id' => 'int',
        'special_price_start' => 'date',
        'special_price_end' => 'date',
        'special_price' => 'float'
    ];

    protected $fillable = [
        'title',
        'slug',
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
        'special_price_end'
    ];

    public const likeRows = [
        'title',
        'slug',
        'summary',
        'description',
        'stock',
        'sizes.name',
        'condition.status',
        'status',
        'price',
        'discount',
        'brand.title',
        'color',
    ];

    /**
     * Get product by slug.
     *
     * @param  string  $slug
     * @return object|null
     */
    public static function getProductBySlug(string $slug): ?object
    {
        return Product::with(['getReview', 'categories'])->whereSlug($slug)->firstOrFail();
    }

    /**
     * Count active products.
     *
     * @return int
     */
    public static function countActiveProduct(): int
    {
        return Product::where('status', 'active')->count() ?: 0;
    }

    /**
     * Get feed items.
     *
     * @return Collection
     */
    public static function getFeedItems(): Collection
    {
        return Product::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get the brand associated with the product.
     *
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the carts associated with the product.
     *
     * @return HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the product reviews associated with the product.
     *
     * @return HasMany
     */
    public function product_reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the wishlists associated with the product.
     *
     * @return HasMany
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the categories associated with the product.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the reviews associated with the product.
     *
     * @return HasMany
     */
    public function getReview(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')
            ->with('user')
            ->where('status', 'active')
            ->orderBy('id', 'DESC');
    }

    /**
     * Increment the slug if it already exists.
     *
     * @param  string  $slug
     * @return string
     */
    public function incrementSlug(string $slug): string
    {
        $original = $slug;
        $count = 2;
        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-".$count++;
        }

        return $slug;
    }

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        return $mediaItem ? $mediaItem->first()->getUrl() : 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    /**
     * Get the image thumbnail URL attribute.
     *
     * @return string|null
     */
    public function getImageThumbUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');
        return $mediaItem ? $mediaItem->first()->getUrl() : 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    /**
     * Get the condition associated with the product.
     *
     * @return BelongsTo
     */
    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * Get the sizes associated with the product.
     *
     * @return BelongsToMany
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class);
    }

    /**
     * Get the tags associated with the product.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the current price of the product.
     *
     * @return float|int|null
     */
    public function getCurrentPrice(): float|int|null
    {
        $today = now();

        if ($this->special_price && $today->between($this->special_price_start, $this->special_price_end)) {
            return $this->special_price;
        }

        return $this->price;
    }

    /**
     * Get the attribute values associated with the product.
     *
     * @return BelongsToMany
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_value')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Get the bundles associated with the product.
     *
     * @return BelongsToMany
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class)->withTimestamps();
    }

    /**
     * Make all searchable using the query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('categories', 'brand', 'sizes', 'condition');
    }

    /**
     * Convert the product to a searchable array.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return $this->toArray();
    }

    /**
     * Get the mapping properties for the product.
     *
     * @return array<string, mixed>
     */
    public function mappableAs(): array
    {
        return [
            'properties' => [
                'id' => [
                    'type' => 'integer',
                ],
                'title' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                ],
                'description' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                ],
                'price' => [
                    'type' => 'float',
                ],
                'color' => [
                    'type' => 'keyword',
                ],
                'status' => [
                    'type' => 'keyword',
                ],
                // Assuming categories and brands are nested objects
                'categories' => [
                    'type' => 'nested',
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'standard',
                        ],
                    ],
                ],
                'brands' => [
                    'type' => 'nested',
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'standard',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the index settings for the product.
     *
     * @return array<string, mixed>
     */
    public function indexSettings(): array
    {
        return [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'standard',
                    ],
                ],
            ],
        ];
    }

}
