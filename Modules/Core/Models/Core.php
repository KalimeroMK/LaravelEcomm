<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ClearsResponseCache;

class Core extends Model
{
    use ClearsResponseCache;
}