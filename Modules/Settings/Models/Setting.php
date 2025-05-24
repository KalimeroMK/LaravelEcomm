<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Settings\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\Settings\Database\Factories\SettingFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Setting
 *
 * @property int                              $id
 * @property string                           $description
 * @property string                           $short_des
 * @property string                           $logo
 * @property string                           $address
 * @property string                           $phone
 * @property string                           $email
 * @property string                           $site-name
 * @property string|null                      $keywords
 * @property string|null                      $google-site-verification
 * @property Carbon|null                      $created_at
 * @property Carbon|null                      $updated_at
 * @property string|null                      $longitude
 * @property string|null                      $latitude
 * @property string|null                      $google_map_api_key
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null                    $media_count
 *
 * @method static Builder<static>|Setting newModelQuery()
 * @method static Builder<static>|Setting newQuery()
 * @method static Builder<static>|Setting query()
 * @method static Builder<static>|Setting whereAddress($value)
 * @method static Builder<static>|Setting whereCreatedAt($value)
 * @method static Builder<static>|Setting whereDescription($value)
 * @method static Builder<static>|Setting whereEmail($value)
 * @method static Builder<static>|Setting whereGoogleMapApiKey($value)
 * @method static Builder<static>|Setting whereGoogleSiteVerification($value)
 * @method static Builder<static>|Setting whereId($value)
 * @method static Builder<static>|Setting whereKeywords($value)
 * @method static Builder<static>|Setting whereLatitude($value)
 * @method static Builder<static>|Setting whereLogo($value)
 * @method static Builder<static>|Setting whereLongitude($value)
 * @method static Builder<static>|Setting wherePhone($value)
 * @method static Builder<static>|Setting whereShortDes($value)
 * @method static Builder<static>|Setting whereSiteName($value)
 * @method static Builder<static>|Setting whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Setting extends Core implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'settings';

    protected $fillable
        = [
            'description',
            'short_des',
            'logo',
            'photo',
            'address',
            'phone',
            'email',
            'site-name',
            'fb_app_id',
            'keywords',
            'google-site-verification',
            'longitude',
            'latitude',
            'google_map_api_key',

        ];

    public static function Factory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
