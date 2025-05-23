<?php

declare(strict_types=1);

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProductSize
 *
 * @property-read Product|null $product
 *
 * @method static Builder<static>|ProductSize newModelQuery()
 * @method static Builder<static>|ProductSize newQuery()
 * @method static Builder<static>|ProductSize query()
 *
 * @mixin Eloquent
 */
class ProductSize extends Model
{
    public $timestamps = false;

    protected $table = 'product_size';

    protected $casts = [
        'size_id' => 'int',
        'product_id' => 'int',
    ];

    protected $fillable = [
        'size_id',
        'product_id',
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
