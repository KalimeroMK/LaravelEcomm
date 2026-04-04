<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Banner\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Modules\Banner\Database\Factories\BannerFactory;
use Modules\Category\Models\Category;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null                 $image_url
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null                    $media_count
 * @property-read Collection<int, Category>   $categories
 * @property-read Carbon|null                 $active_from
 * @property-read int|null                    $max_clicks
 * @property-read int|null                    $max_impressions
 * @property-read int|null                    $current_clicks
 * @property-read int|null                    $current_impressions
 * @property-read Carbon|null                 $active_to
 *
 * @method static Builder<static>|Banner newModelQuery()
 * @method static Builder<static>|Banner newQuery()
 * @method static Builder<static>|Banner query()
 * @method static Builder<static>|Banner whereActiveFrom($value)
 * @method static Builder<static>|Banner whereActiveTo($value)
 * @method static Builder<static>|Banner whereCreatedAt($value)
 * @method static Builder<static>|Banner whereCurrentClicks($value)
 * @method static Builder<static>|Banner whereCurrentImpressions($value)
 * @method static Builder<static>|Banner whereDescription($value)
 * @method static Builder<static>|Banner whereId($value)
 * @method static Builder<static>|Banner whereMaxClicks($value)
 * @method static Builder<static>|Banner whereMaxImpressions($value)
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
            'active_from',
            'active_to',
            'max_clicks',
            'max_impressions',
            'current_clicks',
            'current_impressions',
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

        return route('front.placeholder.image', [
            'type' => 'banner',
            'text' => \Illuminate\Support\Str::limit($this->title ?? 'Banner', 25),
        ]);
    }

    /**
     * The categories this banner belongs to.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'banner_category');
    }

    /**
     * Scope: only banners that are currently active.
     * Filters at DB level to avoid loading all banners and filtering in PHP (N+1 / memory waste).
     * Conditions that require runtime counters (max_clicks, max_impressions) are still checked
     * at DB level using column comparisons.
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', 'active')
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('active_from')->orWhere('active_from', '<=', $now);
            })
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('active_to')->orWhere('active_to', '>=', $now);
            })
            ->where(function (Builder $q): void {
                $q->whereNull('max_clicks')->orWhereColumn('current_clicks', '<', 'max_clicks');
            })
            ->where(function (Builder $q): void {
                $q->whereNull('max_impressions')->orWhereColumn('current_impressions', '<', 'max_impressions');
            });
    }

    /**
     * Check if the banner is currently active (by date, clicks, impressions, status).
     */
    public function isActive(): bool
    {
        $now = now();
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->active_from && $now->lt($this->active_from)) {
            return false;
        }
        if ($this->active_to && $now->gt($this->active_to)) {
            return false;
        }
        if ($this->max_clicks !== null && $this->current_clicks >= $this->max_clicks) {
            return false;
        }
        if ($this->max_impressions !== null && $this->current_impressions >= $this->max_impressions) {
            return false;
        }

        return true;
    }

    /**
     * Increment impressions and auto-disable if needed.
     */
    public function incrementImpression(): void
    {
        $this->increment('current_impressions');
        if ($this->max_impressions !== null && $this->current_impressions >= $this->max_impressions) {
            $this->update(['status' => 'inactive']);
        }
    }

    /**
     * Increment clicks and auto-disable if needed.
     */
    public function incrementClick(): void
    {
        $this->increment('current_clicks');
        if ($this->max_clicks !== null && $this->current_clicks >= $this->max_clicks) {
            $this->update(['status' => 'inactive']);
        }
    }
}
