<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Banner\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Banner\Database\Factories\BannerFactory;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Banner
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 *
 * @method static Builder<static>|Banner newModelQuery()
 * @method static Builder<static>|Banner newQuery()
 * @method static Builder<static>|Banner query()
 * @method static Builder<static>|Banner whereCreatedAt($value)
 * @method static Builder<static>|Banner whereDescription($value)
 * @method static Builder<static>|Banner whereId($value)
 * @method static Builder<static>|Banner whereSlug($value)
 * @method static Builder<static>|Banner whereStatus($value)
 * @method static Builder<static>|Banner whereTitle($value)
 * @method static Builder<static>|Banner whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Banner extends Core implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    protected $table = 'banners';

    protected $fillable
        = [
            'title',
            'slug',
            'description',
            'status',
        ];

    public static function Factory(): BannerFactory
    {
        return BannerFactory::new();
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('banner');

        if ($mediaItem instanceof Media) {
            return $mediaItem->getUrl();
        }

        return 'https://placehold.co/600x400@2x.png';
    }
}
