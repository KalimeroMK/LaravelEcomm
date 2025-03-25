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
 * @property Role $role
 *
 * @method static Builder|ModelHasRole newModelQuery()
 * @method static Builder|ModelHasRole newQuery()
 * @method static Builder|ModelHasRole query()
 * @method static Builder|ModelHasRole whereModelId($value)
 * @method static Builder|ModelHasRole whereModelType($value)
 * @method static Builder|ModelHasRole whereRoleId($value)
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
