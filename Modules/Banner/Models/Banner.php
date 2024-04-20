<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Banner\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Modules\Banner\Database\Factories\BannerFactory;
use Modules\Core\Models\Core;

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
 * @package App\Models
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
 * @mixin Eloquent
 */
class Banner extends Core
{
    protected $table = 'banners';

    protected $fillable = [
        'title',
        'slug',
        'photo',
        'description',
        'status',
    ];

    /**
     * @return BannerFactory
     */
    public static function Factory(): BannerFactory
    {
        return BannerFactory::new();
    }

    /**
     * @param  string  $slug
     *
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
}
