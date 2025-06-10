<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Attribute\Database\Factories\AttributeValueFactory;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

/**
 * Class AttributeValue
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $attributable
 * @property-read Attribute      $attribute
 * @property-read Product        $product
 *
 * @method static Builder<static>|AttributeValue newModelQuery()
 * @method static Builder<static>|AttributeValue newQuery()
 * @method static Builder<static>|AttributeValue query()
 * @method static Builder<static>|AttributeValue whereAttributeId($value)
 * @method static Builder<static>|AttributeValue whereCreatedAt($value)
 * @method static Builder<static>|AttributeValue whereId($value)
 * @method static Builder<static>|AttributeValue whereProductId($value)
 * @method static Builder<static>|AttributeValue whereUpdatedAt($value)
 * @method static Builder<static>|AttributeValue whereValue($value)
 *
 * @mixin Eloquent
 */
class AttributeValue extends Core
{
    protected $table = 'attribute_values';

    protected $fillable
        = [
            'product_id',
            'attribute_id',
            'text_value',
            'boolean_value',
            'date_value',
            'integer_value',
            'float_value',
            'string_value',
            'url_value',
            'hex_value',
            'decimal_value',
        ];

    public static function factory(): AttributeValueFactory
    {
        return AttributeValueFactory::new();
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @return BelongsTo<Attribute, AttributeValue>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @return BelongsTo<Product, AttributeValue>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany|self
    {
        return $this->hasMany(AttributeOption::class);
    }
}
