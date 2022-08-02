<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Settings\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Settings\Database\Factories\SettingFactory;

/**
 * Class Setting
 *
 * @property int $id
 * @property string $description
 * @property string $short_des
 * @property string $logo
 * @property string $photo
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereAddress($value)
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereDescription($value)
 * @method static Builder|Setting whereEmail($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereLogo($value)
 * @method static Builder|Setting wherePhone($value)
 * @method static Builder|Setting wherePhoto($value)
 * @method static Builder|Setting whereShortDes($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Setting extends Core
{
    use HasFactory;
    
    protected $table = 'settings';
    
    protected $fillable = [
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
    
    ];
    
    /**
     * @return SettingFactory
     */
    public static function Factory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
