<?php

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Tag\Models\Tag;

/**
 * Class ProductTag
 *
 * @property int $product_id
 * @property int $tag_id
 * @property Product $product
 * @property Tag $tag
 */
class ProductTag extends Model
{
    protected $table = 'product_tag';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'product_id' => 'int',
        'tag_id' => 'int',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
