<?php

namespace Modules\Attribute\Models;

use App\Models\AttributeValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Attribute\Database\Factories\AttributeFactory;
use Modules\Core\Models\Core;

/**
 * Class Attribute
 *
 * @property int $id
 * @property string|null $name
 * @property string $code
 * @property string $type
 * @property string $display
 * @property bool $filterable
 * @property bool $configurable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|AttributeValue[] $attribute_values
 *
 */
class Attribute extends Core
{
    protected $table = 'attributes';

    protected $casts = [
        'filterable' => 'bool',
        'configurable' => 'bool'
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'display',
        'filterable',
        'configurable'
    ];

    const TYPE_URL = 'url';
    const TYPE_HEX = 'hex';
    const TYPE_TEXT = 'text';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DECIMAL = 'decimal';

    const DISPLAY_INPUT = 'input';
    const DISPLAY_RADIO = 'radio';
    const DISPLAY_COLOR = 'color';
    const DISPLAY_BUTTON = 'button';
    const DISPLAY_SELECT = 'select';
    const DISPLAY_CHECKBOX = 'checkbox';
    const DISPLAY_MULTI_SELECT = 'multiselect';

    const TYPES = [
        self::TYPE_URL,
        self::TYPE_HEX,
        self::TYPE_TEXT,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_FLOAT,
        self::TYPE_INTEGER,
        self::TYPE_BOOLEAN,
        self::TYPE_DECIMAL,
    ];

    const DISPLAYS = [
        self::DISPLAY_INPUT,
        self::DISPLAY_RADIO,
        self::DISPLAY_COLOR,
        self::DISPLAY_BUTTON,
        self::DISPLAY_SELECT,
        self::DISPLAY_CHECKBOX,
        self::DISPLAY_MULTI_SELECT,
    ];

    /**
     * @return AttributeFactory
     */
    public static function Factory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * @throws Exception
     */
    public function getValueColumnName(): string
    {
        return match ($this->type) {
            self::TYPE_URL => 'url_value',
            self::TYPE_HEX => 'hex_value',
            self::TYPE_TEXT => 'text_value',
            self::TYPE_DATE => 'date_value',
            self::TYPE_TIME => 'time_value',
            self::TYPE_FLOAT => 'float_value',
            self::TYPE_STRING => 'string_value',
            self::TYPE_INTEGER => 'integer_value',
            self::TYPE_BOOLEAN => 'boolean_value',
            self::TYPE_DECIMAL => 'decimal_value',
            default => throw new Exception("Invalid attribute type"),
        };
    }
}