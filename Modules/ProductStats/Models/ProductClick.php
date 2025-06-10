<?php

namespace Modules\ProductStats\Models;

use Illuminate\Database\Eloquent\Model;

class ProductClick extends Model
{
    protected $fillable
        = [
            'product_id',
            'user_id',
            'ip_address',
        ];
}
