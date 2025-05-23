<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Role\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Permission\Models\Permission;

/**
 * Class RoleHasPermission
 *
 * @property int $permission_id
 * @property int $role_id
 * @property-read Permission $permission
 * @property-read Role       $role
 *
 * @method static Builder<static>|RoleHasPermission newModelQuery()
 * @method static Builder<static>|RoleHasPermission newQuery()
 * @method static Builder<static>|RoleHasPermission query()
 * @method static Builder<static>|RoleHasPermission wherePermissionId($value)
 * @method static Builder<static>|RoleHasPermission whereRoleId($value)
 *
 * @mixin Eloquent
 */
class RoleHasPermission extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'role_has_permissions';

    protected $casts = [
        'permission_id' => 'int',
        'role_id' => 'int',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
