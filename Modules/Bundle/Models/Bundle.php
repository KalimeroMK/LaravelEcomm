<?php

declare(strict_types=1);

namespace Modules\Bundle\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Modules\Bundle\Database\Factories\BundleFactory;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null                 $image_url
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null                    $media_count
 * @property-read Collection<int, Product>    $products
 * @property-read int|null                    $products_count
 *
 * @method static Builder<static>|Bundle newModelQuery()
 * @method static Builder<static>|Bundle newQuery()
 * @method static Builder<static>|Bundle query()
 * @method static Builder<static>|Bundle whereCreatedAt($value)
 * @method static Builder<static>|Bundle whereDescription($value)
 * @method static Builder<static>|Bundle whereId($value)
 * @method static Builder<static>|Bundle whereName($value)
 * @method static Builder<static>|Bundle wherePrice($value)
 * @method static Builder<static>|Bundle whereSlug($value)
 * @method static Builder<static>|Bundle whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Bundle extends Core implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'bundles';

    protected $casts
        = [
            'price' => 'float',
        ];

    protected $fillable
        = [
            'name',
            'description',
            'price',
            'slug',
        ];

    public static function Factory(): BundleFactory
    {
        return BundleFactory::new();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('bundle');

        return $mediaItem?->getUrl() ?? 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    public function getPreviewImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('bundle');

        return $mediaItem?->getUrl('preview') ?? 'https://via.placeholder.com/300x300.png?text=preview';
    }
}
