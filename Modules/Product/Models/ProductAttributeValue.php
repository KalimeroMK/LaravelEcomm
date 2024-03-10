<?php

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Attribute\Models\AttributeValue;

/**
 * Class ProductAttributeValue
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_value_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property AttributeValue $attribute_value
 * @property Product $product
 *
 * @package App\Models
 */
class ProductAttributeValue extends Model
{
    protected $table = 'product_attribute_value';

    protected $casts = [
        'product_id' => 'int',
        'attribute_value_id' => 'int'
    ];

    protected $fillable = [
        'product_id',
        'attribute_value_id'
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
