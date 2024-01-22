<?php

namespace Modules\Bundle\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

class Bundle extends Core
{
    protected $fillable = [
        'name',
        'description',
        'price'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
