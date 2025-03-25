<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Brand\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Laravel\Scout\Searchable;
use Modules\Brand\Database\Factories\BrandFactory;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Product\Models\Product;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Brand
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|Product[] $products
 * @property-read int|null        $products_count
 *
 * @method static Builder|Brand newModelQuery()
 * @method static Builder|Brand newQuery()
 * @method static Builder|Brand query()
 * @method static Builder|Brand whereCreatedAt($value)
 * @method static Builder|Brand whereId($value)
 * @method static Builder|Brand whereSlug($value)
 * @method static Builder|Brand whereStatus($value)
 * @method static Builder|Brand whereTitle($value)
 * @method static Builder|Brand whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property string $photo
 *
 * @method static Builder|Brand wherePhoto($value)
 */
class Brand extends Core implements Aliased, Explored, HasMedia, IndexSettings
{
    use HasSlug;
    use InteractsWithMedia;
    use Searchable;

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

    /**
     * Converts the model instance to an array format suitable for search indexing.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return $this->toArray();
    }

    /**
     * Defines the mapping for the search engine (Elasticsearch).
     *
     * @return array<string, array<string, mixed>>
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
            ],
        ];
    }

    /**
     * Configuration settings for the search index.
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
