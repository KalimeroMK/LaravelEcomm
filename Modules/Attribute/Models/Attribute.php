<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

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
 * @property Collection|AttributeValue[] $attribute_values
 */
class Attribute extends Core
{
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

    public const DISPLAY_INPUT = 'input';

    public const DISPLAY_RADIO = 'radio';

    public const DISPLAY_COLOR = 'color';

    public const DISPLAY_BUTTON = 'button';

    public const DISPLAY_SELECT = 'select';

    public const DISPLAY_CHECKBOX = 'checkbox';

    public const DISPLAY_MULTI_SELECT = 'multiselect';

    public const TYPES = [
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

    public const DISPLAYS = [
        self::DISPLAY_INPUT,
        self::DISPLAY_RADIO,
        self::DISPLAY_COLOR,
        self::DISPLAY_BUTTON,
        self::DISPLAY_SELECT,
        self::DISPLAY_CHECKBOX,
        self::DISPLAY_MULTI_SELECT,
    ];

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'code',
        'type',
        'display',
        'filterable',
        'configurable',
    ];

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
            default => throw new Exception('Invalid attribute type'),
        };
    }
}
