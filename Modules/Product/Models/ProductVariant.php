<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ProductVariant
 * Pivot model for configurable product - variant relationship
 *
 * @property int $id
 * @property int $product_id
 * @property int $variant_product_id
 * @property array $attribute_combination
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 * @property-read Product $variant
 *
 * @method static Builder<static>|ProductVariant newModelQuery()
 * @method static Builder<static>|ProductVariant newQuery()
 * @method static Builder<static>|ProductVariant query()
 * @method static Builder<static>|ProductVariant whereProductId($value)
 * @method static Builder<static>|ProductVariant whereVariantProductId($value)
 * @method static Builder<static>|ProductVariant default()
 *
 * @mixin Eloquent
 */
class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_product_id',
        'attribute_combination',
        'is_default',
    ];

    protected $casts = [
        'attribute_combination' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Parent configurable product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Variant product
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'variant_product_id');
    }

    /**
     * Scope for default variants
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
