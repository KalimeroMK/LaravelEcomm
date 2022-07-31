<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Admin\Models;

use Modules\Core\Models\Core;

class Condition extends Core
{
    protected $table = 'conditions';
    
    protected $fillable = [
        'status',
    ];
    
}
