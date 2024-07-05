<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Google2fa\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Core;

/**
 * Class LoginSecurity
 *
 * @property int $id
 * @property int $user_id
 * @property bool $google2fa_enable
 * @property string|null $google2fa_secret
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Google2fa newModelQuery()
 * @method static Builder|Google2fa newQuery()
 * @method static Builder|Google2fa query()
 * @method static Builder|Google2fa whereCreatedAt($value)
 * @method static Builder|Google2fa whereGoogle2faEnable($value)
 * @method static Builder|Google2fa whereGoogle2faSecret($value)
 * @method static Builder|Google2fa whereId($value)
 * @method static Builder|Google2fa whereUpdatedAt($value)
 * @method static Builder|Google2fa whereUserId($value)
 *
 * @mixin Eloquent
 */
class Google2fa extends Core
{
    protected $table = 'login_securities';

    protected $casts = [
        'user_id' => 'int',
        'google2fa_enable' => 'bool',
    ];

    protected $hidden = [
        'google2fa_secret',
    ];

    protected $fillable = [
        'user_id',
        'google2fa_enable',
        'google2fa_secret',
    ];
}
