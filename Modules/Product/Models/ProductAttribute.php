<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;

class ProductAttribute extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'attribute_id',
        'configurable',
    ];
    
    protected $with = ['attribute', 'values'];
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
    
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }
}
