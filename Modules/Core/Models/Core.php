<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ClearsCache;

/**
 * @method static Builder<static>|Core newModelQuery()
 * @method static Builder<static>|Core newQuery()
 * @method static Builder<static>|Core query()
 *
 * @mixin Eloquent
 */
class Core extends Model
{
    use ClearsCache;
}
