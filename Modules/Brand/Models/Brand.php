<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Brand\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Brand\Database\Factories\BrandFactory;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Product\Models\Product;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Brand
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Product> $products
 * @property-read int|null $products_count
 *
 * @method static Builder<static>|Brand newModelQuery()
 * @method static Builder<static>|Brand newQuery()
 * @method static Builder<static>|Brand query()
 * @method static Builder<static>|Brand whereCreatedAt($value)
 * @method static Builder<static>|Brand whereId($value)
 * @method static Builder<static>|Brand whereSlug($value)
 * @method static Builder<static>|Brand whereStatus($value)
 * @method static Builder<static>|Brand whereTitle($value)
 * @method static Builder<static>|Brand whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Brand extends Core implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $table = 'brands';

    protected $fillable
        = [
            'title',
            'slug',
            'status',
            'photo',
        ];

    // Factory method for creating Brand instances
    public static function Factory(): BrandFactory
    {
        return BrandFactory::new();
    }

    /**
     * Retrieve a Brand model with its associated products based on the slug.
     *
     * @param  string  $slug  The slug used to find a specific brand.
     */
    public static function getProductByBrand(string $slug): Model|Builder|null
    {
        return self::with('products')->where('slug', $slug)->first();
    }

    // Relationship to the products that belong to this brand
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Increment the slug if it already exists.
     *
     * @param  string  $slug  The base slug.
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

    // Make all products of the brand searchable using this model
    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('products');
    }
}
