<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

class Condition extends Core
{
    protected $table = 'conditions';
    
    protected $fillable = [
        'status',
    ];
    
    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
    
}
