<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Attribute\Database\Factories\AttributeFactory;
use Modules\Core\Models\Core;

/**
 * Class Attribute
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property string|null $display
 * @property int $is_required
 * @property int $is_filterable
 * @property int $is_configurable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|AttributeGroup[]      $groups
 * @property-read Collection<int, AttributeOption> $options
 * @property-read int|null                         $options_count
 * @property-read Collection<int, AttributeValue>  $values
 * @property-read int|null                         $values_count
 *
 * @method static Builder<static>|Attribute newModelQuery()
 * @method static Builder<static>|Attribute newQuery()
 * @method static Builder<static>|Attribute query()
 * @method static Builder<static>|Attribute whereAttributeGroupId($value)
 * @method static Builder<static>|Attribute whereCode($value)
 * @method static Builder<static>|Attribute whereCreatedAt($value)
 * @method static Builder<static>|Attribute whereDisplay($value)
 * @method static Builder<static>|Attribute whereId($value)
 * @method static Builder<static>|Attribute whereIsConfigurable($value)
 * @method static Builder<static>|Attribute whereIsFilterable($value)
 * @method static Builder<static>|Attribute whereIsRequired($value)
 * @method static Builder<static>|Attribute whereName($value)
 * @method static Builder<static>|Attribute whereType($value)
 * @method static Builder<static>|Attribute whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Attribute extends Core
{
    /** @var string[] */
    public const TYPES
        = [
            self::TYPE_URL,
            self::TYPE_HEX,
            self::TYPE_TEXT,
            self::TYPE_DATE,
            self::TYPE_TIME,
            self::TYPE_FLOAT,
            self::TYPE_INTEGER,
            self::TYPE_BOOLEAN,
            self::TYPE_DECIMAL,
            self::TYPE_STRING,
        ];

    public const TYPE_URL = 'url';

    public const TYPE_HEX = 'hex';

    public const TYPE_TEXT = 'text';

    public const TYPE_DATE = 'date';

    public const TYPE_TIME = 'time';

    public const TYPE_FLOAT = 'float';

    public const TYPE_STRING = 'string';

    public const TYPE_INTEGER = 'integer';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_DECIMAL = 'decimal';

    /** @var string[] */
    public const DISPLAYS
        = [
            self::DISPLAY_INPUT,
            self::DISPLAY_RADIO,
            self::DISPLAY_COLOR,
            self::DISPLAY_BUTTON,
            self::DISPLAY_SELECT,
            self::DISPLAY_CHECKBOX,
            self::DISPLAY_MULTI_SELECT,
        ];

    public const DISPLAY_INPUT = 'input';

    public const DISPLAY_RADIO = 'radio';

    public const DISPLAY_COLOR = 'color';

    public const DISPLAY_BUTTON = 'button';

    public const DISPLAY_SELECT = 'select';

    public const DISPLAY_CHECKBOX = 'checkbox';

    public const DISPLAY_MULTI_SELECT = 'multiselect';

    protected $table = 'attributes';

    protected $fillable
        = [
            'name',
            'code',
            'type',
            'display',
            'is_filterable',
            'is_configurable',
            'is_required',
        ];

    public static function factory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    /**
     * @return BelongsToMany<AttributeGroup, Attribute>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(AttributeGroup::class, 'attribute_attribute_group', 'attribute_id',
            'attribute_group_id');
    }

    /**
     * @return BelongsToMany<AttributeSet, Attribute>
     */
    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(AttributeSet::class, 'attribute_attribute_set', 'attribute_id',
            'attribute_set_id');
    }

    /**
     * @return HasMany<AttributeValue, Attribute>
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * @return HasMany<AttributeOption, Attribute>
     */
    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    /**
     * Get the column name used to store the value depending on type.
     */
    public function getValueColumnName(): ?string
    {
        return match ($this->type) {
            'text' => 'text_value',
            'boolean' => 'boolean_value',
            'date' => 'date_value',
            'integer' => 'integer_value',
            'float' => 'float_value',
            'string' => 'string_value',
            'url' => 'url_value',
            'hex' => 'hex_value',
            'decimal' => 'decimal_value',
            default => null,
        };
    }
}
