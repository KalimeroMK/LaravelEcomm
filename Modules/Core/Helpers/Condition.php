<?php

namespace Modules\Core\Helpers;

use Modules\Core\Models\Core;

class Condition extends Core
{
    protected $table = 'conditions';
    
    protected $fillable = [
        'status',
    ];
}