<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Google2fa\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Google2fa\Database\Factories\Google2faFactory;

/**
 * Class LoginSecurity
 *
 * @property int $id
 * @property int $user_id
 * @property bool $google2fa_enable
 * @property string|null $google2fa_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|Google2fa newModelQuery()
 * @method static Builder<static>|Google2fa newQuery()
 * @method static Builder<static>|Google2fa query()
 * @method static Builder<static>|Google2fa whereCreatedAt($value)
 * @method static Builder<static>|Google2fa whereGoogle2faEnable($value)
 * @method static Builder<static>|Google2fa whereGoogle2faSecret($value)
 * @method static Builder<static>|Google2fa whereId($value)
 * @method static Builder<static>|Google2fa whereUpdatedAt($value)
 * @method static Builder<static>|Google2fa whereUserId($value)
 *
 * @mixin Eloquent
 */
class Google2fa extends Core
{
    use HasFactory;

    protected $table = 'login_securities';

    protected $hidden = [
        'google2fa_secret',
    ];

    protected $casts = [
        'user_id' => 'int',
        'google2fa_enable' => 'bool',
        'recovery_codes' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'google2fa_enable',
        'google2fa_secret',
        'recovery_codes',
    ];

    public function hasRecoveryCode(string $code): bool
    {
        if (! $this->recovery_codes || ! is_array($this->recovery_codes)) {
            return false;
        }

        return in_array($code, $this->recovery_codes, true);
    }

    public function removeRecoveryCode(string $code): void
    {
        if (! $this->recovery_codes || ! is_array($this->recovery_codes)) {
            return;
        }

        $this->recovery_codes = array_values(array_filter($this->recovery_codes, fn ($c) => $c !== $code));
        $this->save();
    }

    protected static function newFactory(): Google2faFactory
    {
        return Google2faFactory::new();
    }
}
