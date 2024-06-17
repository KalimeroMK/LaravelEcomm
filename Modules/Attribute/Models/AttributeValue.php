<?php

namespace Modules\Attribute\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Attribute\Database\Factories\AttributeValueFactory;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

/**
 * Class AttributeValue
 *
 * @property int $id
 * @property string|null $default
 * @property string|null $text_value
 * @property Carbon|null $date_value
 * @property Carbon|null $time_value
 * @property string|null $url_value
 * @property string|null $hex_value
 * @property float|null $float_value
 * @property string|null $string_value
 * @property bool|null $boolean_value
 * @property int|null $integer_value
 * @property float|null $decimal_value
 * @property int $attribute_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Attribute $attribute
 * @property Collection|Product[] $products
 *
 */
class AttributeValue extends Core
{
    protected $table = 'attribute_values';

    protected $casts = [
        'date_value' => 'datetime',
        'time_value' => 'datetime',
        'float_value' => 'float',
        'boolean_value' => 'bool',
        'integer_value' => 'int',
        'decimal_value' => 'float',
        'attribute_id' => 'int'
    ];

    protected $fillable = [
        'default',
        'text_value',
        'date_value',
        'time_value',
        'url_value',
        'hex_value',
        'float_value',
        'string_value',
        'boolean_value',
        'integer_value',
        'decimal_value',
        'attribute_id'
    ];

    protected $appends = ['value'];

    /**
     * @return AttributeValueFactory
     */
    public static function Factory(): AttributeValueFactory
    {
        return AttributeValueFactory::new();
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * @return string
     */
    public function getValueAttribute(): string
    {
        if (!$this->relationLoaded('attribute')) {
            $this->load('attribute');
        }

        $type = $this->attribute->type;
        return match ($type) {
            Attribute::TYPE_URL => $this->url_value,
            Attribute::TYPE_HEX => $this->hex_value,
            Attribute::TYPE_TEXT => $this->text_value,
            default => null,
        };
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_value')
            ->withPivot('id')
            ->withTimestamps();
    }
}