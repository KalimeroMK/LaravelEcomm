<?php

namespace Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Attribute\Database\Factories\AttributeFactory;
use Modules\Core\Models\Core;

class Attribute extends Core
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'display',
        'filterable',
        'configurable',
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
}