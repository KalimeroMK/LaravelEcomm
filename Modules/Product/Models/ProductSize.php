<?php

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Size\Models\Size;

/**
 * Class ProductSize
 *
 * @property int $id
 * @property int $size_id
 * @property int $product_id
 *
 * @property Product $product
 * @property Size $size
 *
 * @package App\Models
 */
class ProductSize extends Model
{
    protected $table = 'product_size';
    public $timestamps = false;

    protected $casts = [
        'size_id' => 'int',
        'product_id' => 'int'
    ];

    protected $fillable = [
        'size_id',
        'product_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
