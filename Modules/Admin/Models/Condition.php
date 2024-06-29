<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Product\Models\Product;

class Condition extends Model
{
    protected $table = 'conditions';

    protected $fillable
        = [
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
