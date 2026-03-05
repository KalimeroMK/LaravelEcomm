<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Modules\Attribute\Database\Factories\AttributeValueFactory;
use Modules\Core\Models\Core;

/**
 * Class AttributeValue
 *
 * @property int $id
 * @property int|null $product_id (deprecated, use attributable)
 * @property int $attributable_id
 * @property string $attributable_type
 * @property int $attribute_id
 * @property string|null $text_value
 * @property bool|null $boolean_value
 * @property Carbon|null $date_value
 * @property int|null $integer_value
 * @property float|null $float_value
 * @property string|null $string_value
 * @property string|null $url_value
 * @property string|null $hex_value
 * @property float|null $decimal_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $attributable
 * @property-read Attribute $attribute
 *
 * @method static Builder<static>|AttributeValue newModelQuery()
 * @method static Builder<static>|AttributeValue newQuery()
 * @method static Builder<static>|AttributeValue query()
 * @method static Builder<static>|AttributeValue whereAttributeId($value)
 * @method static Builder<static>|AttributeValue whereAttributableId($value)
 * @method static Builder<static>|AttributeValue whereAttributableType($value)
 * @method static Builder<static>|AttributeValue whereCreatedAt($value)
 * @method static Builder<static>|AttributeValue whereId($value)
 * @method static Builder<static>|AttributeValue whereUpdatedAt($value)
 * @method static Builder<static>|AttributeValue forProduct($productId)
 * @method static Builder<static>|AttributeValue forModel($model)
 *
 * @mixin Eloquent
 */
class AttributeValue extends Core
{
    protected $table = 'attribute_values';

    protected $fillable = [
        'product_id', // Keep for backward compatibility during migration
        'attributable_id',
        'attributable_type',
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

    protected $casts = [
        'boolean_value' => 'boolean',
        'date_value' => 'date',
        'integer_value' => 'integer',
        'float_value' => 'float',
        'decimal_value' => 'float',
    ];

    public static function factory(): AttributeValueFactory
    {
        return AttributeValueFactory::new();
    }

    /**
     * Get the parent attributable model (polymorphic).
     *
     * @return MorphTo<Model, AttributeValue>
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Attribute, AttributeValue>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the value from the appropriate column based on attribute type.
     */
    public function getValue(): mixed
    {
        if (! $this->attribute) {
            return $this->text_value;
        }

        $column = $this->attribute->getValueColumnName();

        return $this->$column;
    }

    /**
     * Set the value in the appropriate column based on attribute type.
     */
    public function setValue(mixed $value): void
    {
        if (! $this->attribute) {
            $this->text_value = $value;

            return;
        }

        $column = $this->attribute->getValueColumnName();
        $this->$column = $value;
    }

    /**
     * Scope for specific model instance
     */
    public function scopeForModel(Builder $query, Model $model): Builder
    {
        return $query->where('attributable_type', get_class($model))
            ->where('attributable_id', $model->getKey());
    }

    /**
     * Scope for products (backward compatibility)
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where(function ($q) use ($productId) {
            $q->where('product_id', $productId)
                ->orWhere(function ($q2) use ($productId) {
                    $q2->where('attributable_type', 'Modules\\Product\\Models\\Product')
                        ->where('attributable_id', $productId);
                });
        });
    }

    /**
     * Boot method to auto-set polymorphic fields from product_id
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            // If product_id is set but attributable is not, auto-convert
            if ($model->product_id && ! $model->attributable_id) {
                $model->attributable_id = $model->product_id;
                $model->attributable_type = 'Modules\\Product\\Models\\Product';
            }
            // If attributable is set but product_id is not, copy back
            if ($model->attributable_id && ! $model->product_id && $model->attributable_type === 'Modules\\Product\\Models\\Product') {
                $model->product_id = $model->attributable_id;
            }
        });
    }
}
