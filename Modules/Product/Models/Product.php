<?php

namespace Modules\Product\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Kalimeromk\Filterable\app\Traits\Filterable;
use Laravel\Scout\Searchable;
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
 * @property int $d_deal
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
 * @property-read int|null $carts_count
 * @property-read int|null $product_reviews_count
 * @property-read int|null $wishlists_count
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read string $image_url
 * @property string|null $color
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
 * @method static Builder|Product whereSize($value)
 * @method static Builder|Product whereSlug($value)
 * @method static Builder|Product whereStatus($value)
 * @method static Builder|Product whereStock($value)
 * @method static Builder|Product whereSummary($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereColor($value)
 *
 * @mixin Eloquent
 */
class Product extends Core implements Aliased, Explored, HasMedia, IndexSettings
{
    use Filterable;
    use HasFactory;
    use HasSlug;
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
        'special_price' => 'float',
        'condition_id' => 'int',
    ];

    protected $fillable = [
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

    public static function Factory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public static function getProductBySlug(string $slug): ?Product
    {
        return Product::with(['getReview', 'categories'])->whereSlug($slug)->firstOrFail();
    }

    public static function countActiveProduct(): int
    {
        $data = Product::whereStatus('active')->count();

        return $data ?: 0;
    }

    public static function getFeedItems(): \Illuminate\Support\Collection
    {
        return Product::orderBy('created_at', 'desc')->limit(20)->get();
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

        return $mediaItem ? $mediaItem->first()->getUrl() : 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    public function getImageThumbUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('product');

        return $mediaItem ? $mediaItem->first()->getUrl() : 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return HigherOrderBuilderProxy|mixed|null
     */
    public function getCurrentPrice(): mixed
    {
        $today = now();

        return $this->special_price && $today->between($this->special_price_start,
            $this->special_price_end) ? $this->special_price : null;
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class,
            'product_attribute_value')->withPivot('id')->withTimestamps();
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class)->withTimestamps();
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('categories', 'brand', 'sizes', 'condition');
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function mappableAs(): array
    {
        return [
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'text', 'analyzer' => 'standard'],
                'description' => ['type' => 'text', 'analyzer' => 'standard'],
                'price' => ['type' => 'float'],
                'color' => ['type' => 'keyword'],
                'status' => ['type' => 'keyword'],
                'categories' => [
                    'type' => 'nested',
                    'properties' => [
                        'title' => ['type' => 'text', 'analyzer' => 'standard'],
                    ],
                ],
                'brands' => [
                    'type' => 'nested',
                    'properties' => [
                        'title' => ['type' => 'text', 'analyzer' => 'standard'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function indexSettings(): array
    {
        return [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'analysis' => [
                'analyzer' => [
                    'default' => ['type' => 'standard'],
                ],
            ],
        ];
    }

    public function searchBrandsByProduct(string $searchTerm): \Illuminate\Support\Collection
    {
        $productBrandIds = Product::search($searchTerm)->get()->pluck('brand_id')->unique();

        return Brand::whereIn('id', $productBrandIds)->get();
    }

    public function attributes()
    {
        return $this->morphMany(AttributeValue::class, 'attributable');
    }
}
