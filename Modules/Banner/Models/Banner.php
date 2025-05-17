<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Banner\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Banner\Database\Factories\BannerFactory;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Banner
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $photo
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Banner newModelQuery()
 * @method static Builder|Banner newQuery()
 * @method static Builder|Banner query()
 * @method static Builder|Banner whereCreatedAt($value)
 * @method static Builder|Banner whereDescription($value)
 * @method static Builder|Banner whereId($value)
 * @method static Builder|Banner wherePhoto($value)
 * @method static Builder|Banner whereSlug($value)
 * @method static Builder|Banner whereStatus($value)
 * @method static Builder|Banner whereTitle($value)
 * @method static Builder|Banner whereUpdatedAt($value)
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
