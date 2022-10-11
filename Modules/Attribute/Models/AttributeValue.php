<?php

namespace Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Attribute\Database\Factories\AttributeValueFactory;
use Modules\Core\Models\Core;

class AttributeValue extends Core
{
    protected $fillable = [
        'default',
        'url_value',
        'hex_value',
        'date_value',
        'time_value',
        'text_value',
        'float_value',
        'string_value',
        'integer_value',
        'boolean_value',
        'decimal_value',
        'attribute_id',
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
    
    public function getValueAttribute()
    {
        return $this->{$this->attribute->type . '_value'};
    }
}