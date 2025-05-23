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
use Modules\Tag\Models\Tag;

/**
 * Class ProductTag
 *
 * @property int $product_id
 * @property int $tag_id
 * @property-read Product $product
 * @property-read Tag     $tag
 *
 * @method static Builder<static>|ProductTag newModelQuery()
 * @method static Builder<static>|ProductTag newQuery()
 * @method static Builder<static>|ProductTag query()
 * @method static Builder<static>|ProductTag whereProductId($value)
 * @method static Builder<static>|ProductTag whereTagId($value)
 *
 * @mixin Eloquent
 */
class ProductTag extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'product_tag';

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
