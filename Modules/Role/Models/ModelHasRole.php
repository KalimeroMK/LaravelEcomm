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

/**
 * Class ModelHasRole
 *
 * @property int $role_id
 * @property string $model_type
 * @property int $model_id
 * @property-read Role $role
 *
 * @method static Builder<static>|ModelHasRole newModelQuery()
 * @method static Builder<static>|ModelHasRole newQuery()
 * @method static Builder<static>|ModelHasRole query()
 * @method static Builder<static>|ModelHasRole whereModelId($value)
 * @method static Builder<static>|ModelHasRole whereModelType($value)
 * @method static Builder<static>|ModelHasRole whereRoleId($value)
 *
 * @mixin Eloquent
 */
class ModelHasRole extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'model_has_roles';

    protected $casts = [
        'role_id' => 'int',
        'model_id' => 'int',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
