<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ClearsCache;

class Core extends Model
{
    use ClearsCache;
}
