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
use Modules\Attribute\Models\AttributeValue;

/**
 * Class ProductAttributeValue
 *
 * @property-read AttributeValue|null $attribute_value
 * @property-read Product|null        $product
 *
 * @method static Builder<static>|ProductAttributeValue newModelQuery()
 * @method static Builder<static>|ProductAttributeValue newQuery()
 * @method static Builder<static>|ProductAttributeValue query()
 *
 * @mixin Eloquent
 */
class ProductAttributeValue extends Model
{
    protected $table = 'product_attribute_value';

    protected $casts = [
        'product_id' => 'int',
        'attribute_value_id' => 'int',
    ];

    protected $fillable = [
        'product_id',
        'attribute_value_id',
    ];

    public function attribute_value(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
