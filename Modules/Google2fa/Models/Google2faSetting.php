<?php

declare(strict_types=1);

namespace Modules\Google2fa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property bool $enforce_for_admins
 * @property bool $enforce_for_users
 * @property array|null $enforced_roles
 * @property int $recovery_codes_count
 * @property bool $require_backup_codes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Google2faSetting extends Core
{
    use HasFactory;

    protected $table = 'google2fa_settings';

    protected $casts = [
        'enforce_for_admins' => 'bool',
        'enforce_for_users' => 'bool',
        'enforced_roles' => 'array',
        'recovery_codes_count' => 'int',
        'require_backup_codes' => 'bool',
    ];

    protected $fillable = [
        'enforce_for_admins',
        'enforce_for_users',
        'enforced_roles',
        'recovery_codes_count',
        'require_backup_codes',
    ];

    public static function getSettings(): self
    {
        return self::firstOrCreate([], [
            'enforce_for_admins' => false,
            'enforce_for_users' => false,
            'enforced_roles' => [],
            'recovery_codes_count' => 10,
            'require_backup_codes' => true,
        ]);
    }

    public function shouldEnforceForRole(string $role): bool
    {
        if ($this->enforced_roles === null) {
            return false;
        }

        return in_array($role, $this->enforced_roles, true);
    }
}
