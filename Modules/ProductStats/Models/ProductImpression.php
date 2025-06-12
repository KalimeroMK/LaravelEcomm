<?php

declare(strict_types=1);

namespace Modules\ProductStats\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImpression extends Model
{
    protected $fillable
        = [
            'product_id',
            'user_id',
            'ip_address',
        ];
}
