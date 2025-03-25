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
 * Class ModelHasPermission
 *
 * @property int $permission_id
 * @property string $model_type
 * @property int $model_id
 * @property Permission $permission
 *
 * @method static Builder|ModelHasPermission newModelQuery()
 * @method static Builder|ModelHasPermission newQuery()
 * @method static Builder|ModelHasPermission query()
 * @method static Builder|ModelHasPermission whereModelId($value)
 * @method static Builder|ModelHasPermission whereModelType($value)
 * @method static Builder|ModelHasPermission wherePermissionId($value)
 *
 * @mixin Eloquent
 */
class ModelHasPermission extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'model_has_permissions';

    protected $casts = [
        'permission_id' => 'int',
        'model_id' => 'int',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
