<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Attribute\Database\Factories\AttributeValueFactory;
use Modules\Core\Models\Core;

/**
 * Class AttributeValue
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string|null $text_value
 * @property bool|null $boolean_value
 * @property \Illuminate\Support\Carbon|null $date_value
 * @property int|null $integer_value
 * @property float|null $float_value
 * @property string|null $string_value
 * @property string|null $url_value
 * @property string|null $hex_value
 * @property float|null $decimal_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|Eloquent $attributable
 * @property-read Attribute                                    $attribute
 * @property-read string|null                                  $value
 *
 * @method static Builder<static>|AttributeValue newModelQuery()
 * @method static Builder<static>|AttributeValue newQuery()
 * @method static Builder<static>|AttributeValue query()
 * @method static Builder<static>|AttributeValue whereAttributeId($value)
 * @method static Builder<static>|AttributeValue whereBooleanValue($value)
 * @method static Builder<static>|AttributeValue whereCreatedAt($value)
 * @method static Builder<static>|AttributeValue whereDateValue($value)
 * @method static Builder<static>|AttributeValue whereDecimalValue($value)
 * @method static Builder<static>|AttributeValue whereFloatValue($value)
 * @method static Builder<static>|AttributeValue whereHexValue($value)
 * @method static Builder<static>|AttributeValue whereId($value)
 * @method static Builder<static>|AttributeValue whereIntegerValue($value)
 * @method static Builder<static>|AttributeValue whereProductId($value)
 * @method static Builder<static>|AttributeValue whereStringValue($value)
 * @method static Builder<static>|AttributeValue whereTextValue($value)
 * @method static Builder<static>|AttributeValue whereUpdatedAt($value)
 * @method static Builder<static>|AttributeValue whereUrlValue($value)
 *
 * @mixin Eloquent
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
        'attribute_id' => 'int',
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
        'attribute_id',
        'product_id', // <-- Added for seeder mass-assignment
    ];

    protected $appends = ['value'];

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
     * @return MorphTo<Model, AttributeValue>
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getValueAttribute(): ?string
    {
        if (! $this->relationLoaded('attribute')) {
            $this->load('attribute');
        }

        $type = $this->attribute->type;

        return match ($type) {
            Attribute::TYPE_URL => $this->url_value,
            Attribute::TYPE_HEX => $this->hex_value,
            Attribute::TYPE_TEXT => $this->text_value,
            Attribute::TYPE_DATE => $this->date_value ? $this->date_value->toDateString() : '',
            Attribute::TYPE_TIME => $this->time_value ? $this->time_value->toTimeString() : '',
            Attribute::TYPE_FLOAT => $this->float_value !== null ? (string) $this->float_value : '',
            Attribute::TYPE_STRING => $this->string_value,
            Attribute::TYPE_INTEGER => $this->integer_value !== null ? (string) $this->integer_value : '',
            Attribute::TYPE_BOOLEAN => $this->boolean_value ? 'true' : 'false',
            Attribute::TYPE_DECIMAL => $this->decimal_value !== null ? (string) $this->decimal_value : '',
            default => $this->default ?? '',
        };
    }
}
