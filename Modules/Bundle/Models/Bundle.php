<?php

namespace Modules\Bundle\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Bundle\Database\Factories\BundleFactory;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Bundle extends Core implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'bundles';

    protected $casts = [
        'price' => 'float',
    ];

    protected $fillable = [
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
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }

    /**
     * @mixin ImageDriver
     *
     * @method $this nonQueued()
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('bundle');

        if ($mediaItem) {
            return $mediaItem->first()->getUrl();
        }

        return 'https://via.placeholder.com/640x480.png/003311?text=et';
    }
}
